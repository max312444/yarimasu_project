<?php
// 세션 시작
session_start();

// 로그인 여부
# 만약 세션에 사용자아이디가 없으면 로그인 페이지로 돌아감
if (!isset($_SESSION["사용자아이디"])) {
    header("Refresh: 3; url=login3.html");
    echo"로그인 기록이 없어 3초 뒤 로그인 페이지로 돌아갑니다.";
    exit();
}

// 로그인한 사용자가 'admin'인지 확인
$isAdmin = ($_SESSION['사용자아이디'] == 'admin');

// DB 연결 정보
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// DB 연결
# 새로운 MySQL 연결해서 데이터베이스와 동기화
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die('DB 연결 실패: ' . $conn->connect_error);
}

// 로그인한 사용자 아이디
# 변수에 사용자아이디 저장 - 작성자 자동 기입, 로그인한 사용자를 화면 상단에 표시하기 위해서 변수 생성
$로그인한사용자 = $_SESSION['사용자아이디'];

// 페이지네이션 설정
# 한 페이지당 10개 표시를 위해 변수 생성
$게시글_페이지당_개수 = 10;
# 기본 값으로 1페이지 (GET 요청에서 page 값을 가져와 현재 페이지 결정)
$현재페이지 = isset($_GET['page']) ? intval($_GET['page']) : 1;
# 현재 페이지의 첫 번째 게시글 번호를 계산 (LIMIT에서 사용됨)
$시작게시글번호 = ($현재페이지 - 1) * $게시글_페이지당_개수;

// 검색어 가져오기
# GET 요청에서 검색할 단어를 가져와 변수에 저장 (공백 제거)
$검색어 = isset($_GET['search']) ? trim($_GET['search']) : '';

// 전체 게시글 개수 가져오기 (검색 포함)
# 전체 게시글 개수를 변수에 저장하여 검색 시 게시물 전체를 순회함
$total_sql = "SELECT COUNT(*) AS total FROM 게시판";
# 만약 검색어가 비어있지 않으면 제목 또는 작성자에 검색어가 포함된 게시글만 개수 계산
if (!empty($검색어)) {
    $total_sql .= " WHERE 제목 LIKE '%$검색어%' OR 작성자 LIKE '%$검색어%'";
}
# SQL 실행 후 결과를 가져옴
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$전체게시글수 = $total_row['total'];

# 전체 페이지 수 계산 (총 게시글 개수를 페이지당 개수로 나눈 후 올림 처리)
$전체페이지수 = ceil($전체게시글수 / $게시글_페이지당_개수);

// 현재 페이지에 해당하는 게시물 가져오기 (검색 포함)
$sql = "SELECT * FROM 게시판";
if (!empty($검색어)) {
    $sql .= " WHERE 제목 LIKE '%$검색어%' OR 작성자 LIKE '%$검색어%'";
}
# 최신 게시글이 먼저 보이도록 작성시간 기준 내림차순 정렬 후, 현재 페이지에 해당하는 개수만 가져옴
$sql .= " ORDER BY 작성시간 DESC LIMIT $시작게시글번호, $게시글_페이지당_개수";

$result = $conn->query($sql);
$게시글목록 = [];
while ($row = $result->fetch_assoc()) {
    $게시글목록[] = $row;
}

// 빈칸 채우기
# 한 페이지에 항상 10개의 행을 유지하기 위해, 게시글 개수가 부족하면 빈칸(null) 추가
$빈칸수 = $게시글_페이지당_개수 - count($게시글목록);
for ($i = 0; $i < $빈칸수; $i++) {
    $게시글목록[] = null;
}
?>

<!DOCTYPE html> <!-- 화면 출력 파트 -->
<html lang="ko"> <!-- 언어는 한글로 -->
<head> <!-- 제목 설정 파트 -->
    <meta charset="UTF-8"> <!-- UTF-8 인코딩 (한글 깨짐 방지) -->
    <title>게시판</title> <!-- 제목 -->
</head>
<body> <!-- 내용 파트 -->
    <h2>게시판</h2> <!-- 제목 설정 -->
    <p>안녕하세요 <strong><?= $로그인한사용자 ?></strong>님</p> <!-- 로그인한 사용자가 누구인지 표시 및 환영 메시지 도출 -->
    <a href="logout3.php"><button id="logoutBtn">로그아웃</button></a>

    <div id="board"> <!-- 게시글 표시 파트 -->
        <h3>게시글 목록</h3><!-- 제목 -->

        <!-- 검색 폼 추가 -->
        <form action="main_page3.php" method="GET"> <!-- 검색을 위한 GET 요청 -->
            <input type="text" name="search" placeholder="검색어 입력" value="<?= htmlspecialchars($검색어) ?>"> <!-- 검색어 입력칸 -->
            <button type="submit">검색</button> <!-- 검색 버튼 -->
        </form>

        <table><!-- 표 형식으로 표시 -->
            <thead><!-- 표 머릿글(순번, 제목 같은 정열 순서 알림) -->
                <tr><!-- 머릿글 내용 -->
                    <th>순번</th>
                    <th>제목</th>
                    <th>작성시간</th>
                    <th>작성자</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody><!-- 표 본문 -->
                <?php foreach ($게시글목록 as $index => $row): ?> <!-- 게시글 목록을 하나씩 출력 -->
                    <tr>
                        <?php if ($row): ?> <!-- 게시글이 있는 경우 -->
                            <td><?= $시작게시글번호 + $index + 1 ?></td> <!-- 순번 출력 -->
                            <td><a href="post_view3.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['제목']) ?></a></td> <!-- 제목 클릭 시 해당 게시글 보기 -->
                            <td><?= $row['작성시간'] ?></td> <!-- 작성시간 표시 -->
                            <td><?= htmlspecialchars($row['작성자']) ?></td> <!-- 작성자 표시 -->
                            <td>
                                <?php if ($isAdmin || $row['작성자'] === $로그인한사용자): ?> <!-- 관리자거나 작성자인 경우 수정/삭제 가능 -->
                                    <a href="post_edit3.php?id=<?= $row['id'] ?>">수정</a> |  
                                    <a href="post_delete3.php?id=<?= $row['id'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a> <!-- 삭제 확인 경고창 -->
                                <?php endif; ?>
                            </td>
                        <?php else: ?> <!-- 빈 게시글 공간 -->
                            <td></td><td></td><td></td><td></td>
                        <?php endif; ?> 
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="pagination">
        <?php 
        if ($현재페이지 > 1) { # 현재 페이지가 1보다 크면 이전 버튼 활성화
            echo "<a href='main_page3.php?page=1&search=" . urlencode($검색어) . "'>처음</a>"; # 첫 페이지로 이동
            echo "<a href='main_page3.php?page=" . ($현재페이지 - 1) . "&search=" . urlencode($검색어) . "'>이전</a>"; # 이전 페이지로 이동
        }

        for ($i = 1; $i <= $전체페이지수; $i++) {
            echo "<a href='main_page3.php?page=$i&search=" . urlencode($검색어) . "'>$i</a> ";
        }

        if ($현재페이지 < $전체페이지수) { # 현재 페이지가 마지막 페이지보다 작으면 다음 버튼 활성화
            echo "<a href='main_page3.php?page=" . ($현재페이지 + 1) . "&search=" . urlencode($검색어) . "'>다음</a>";
            echo "<a href='main_page3.php?page=$전체페이지수&search=" . urlencode($검색어) . "'>마지막</a>";
        }
        ?>
    </div>

    <a href="post_create3.php">
        <button>게시물 작성</button>
    </a>
</body>
</html>
<?php
$conn->close(); # DB 연결 종료
?>