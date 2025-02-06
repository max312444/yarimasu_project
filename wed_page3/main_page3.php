<?php
// 세션 시작
session_start();

// 로그인 여부 
if (!isset($_SESSION["사용자아이디"])) {
    header("Location: login3.php");
}

// 로그인한 사용자가 'admin'인지 확인
$isAdmin = ($_SESSION['사용자아이디'] == 'admin');

// DB 연결 정보
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// DB 연결
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die('DB 연결 실패'. $conn->connect_error);
}

// 로그인한 사용자 아이디
$로그인한사용자 = $_SESSION['사용자아이디'];

// 페이지네이션 설정
$게시글_페이지당_개수 = 10; // 한 페이지에 게시글 몇개 할껀지
$현재페이지 = isset($_GET['page']) ? intval($_GET['page']) :1; 
// URL에서 page 값을 가져와 현제 페이지를 설정
// intval($_GET['page'])는 page 값을 정수로 변환해 숫자가 아닌 경우 오류 방지
// 만약 page 값이 없으면 기본적으로 1페이지로 설정
$시작게시글번호 = ($현재페이지 - 1) * $게시글_페이지당_개수;
// MySQL에서 데이터를 가져올 때 몇 번째부터 시작할지 계산
// ex) 현재 페이지가 1이면 1부터 시작. 2라면 20번부터 시작. 5라면 50번부터 시작

// 전체 게시글 개수 가져오기
$total_sql = "SELECT COUNT(*) AS total FROM 게시판";
// 게시판 테이블에서 모든 게시글의 개수를 세어서 total에 저장
$total_result = $conn->query($total_sql);
// MySQL에 쿼리를 실행하고 결과 가져옴
$total_row = $total_result->fetch_assoc();
// 결과를 배열 형태로 변환
$전체게시글수 = $total_row['total']; // 총 게시글 개수 저장

// 현제 페이지 수 계산
$전체페이지수 = ceil($전체게시글수 / $게시글_페이지당_개수); // 여기서 ceil함수는 반올림을 의미함. 소수점을 없애기 위함임

// 현재 페이지에 해당하는 게시물 가져오기
$sql = "SELECT 8 FROM 게시판 ORDER BY 작성시간 DESC LIMIT $시작게시글번호, $게시글_페이지당_개수";
$result = $conn->query($sql);

// 게시글 데이터를 배열에 저장
$게시글목록 = [];
while ($row = $result->fetch_assoc()) {
    $게시글목록 [] = $row;
}

// 화면에 10개의 게시물 칸 생성 - 10칸 될 때 까지 빈칸 생성
$빈칸수 = $게시글_페이지당_개수 - count($게시글목록);
for ($i = 0; $i < $빈칸수; $i++) {
    $게시글목록 [] = null;
}
?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <title>게시판</title>
    </head>
    <body>
        <h2>게시판</h2>
        <p>안녕하세요 <strong><?= $로그인한사용자 ?></strong>님</p>
        <a href="login3.html"><button id="logoutBtn">로그아웃</button></a>

        <div id="board">
            <h3>게시글 목록</h3>
            <table>
                <thead>
                    <tr>
                        <th>순번</th>
                        <th>제목</th>
                        <th>작성시간</th>
                        <th>작성자</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($게시글목록 as $index => $row): ?>
                        <tr>
                            <?php if ($row): ?>
                                <td><?= $시작게시글번호 + $index + 1 ?></td>
                                <td><a href="post_view.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['제목']) ?></a></td>
                                <td><?= $row['작성시간'] ?></td>
                                <td><?= htmlspecialchars($row['작성자']) ?></td>
                                <td>
                                    <?php if ($isAdmin || $row['작성자'] === $로그인한사용자): ?>
                                    <a href="post_edit.php?id=<?= $row['id'] ?>">수정</a> | 
                                    <a href="post_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
                                <?php endif; ?>
                                </td>
                            <?php else: ?>
                                <td></td><td></td><td></td><td></td>
                            <?php endif; ?> 
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- 페이지네이션 -->
         <div class="pagenation">
            <?php 
            $페이지시작 = max(1, $현재페이지 - 2);
            $페이지끝 = min($전체페이지수, $현재페이지 + 2);

            if ($현재페이지 > 1) {
                echo "<a href='main_page3.php?page=1'>처음</a>";
                echo "<a href='main_page3.pgp?page=" . ($현재페이지 - 1) . "'>이전</a>";
            }
            for ($i = $페이지시작 ; $i <= $페이지끝 ; $i++) {
                if ($i == $현재페이지) {
                    echo "<a class='active'>$i</a>";
                } else {
                    echo "<a href='main_page3.php?page=$i'>$i</a>";
                }
            }
            if ($현재페이지 < $전체페이지수) {
                echo "<a href='main_page3.php?page=" . ($현재페이지 + 1) . "'>다음</a>";
                echo "<a href='main_page3.php?page=$전체페이지수 '>마지막</a>";
            }
            ?>
         </div>
         <a href="post_create3.php">
            <button>게시물 작성</button>
         </a>
    </body>
</html>
<?php
$conn->close();
?>