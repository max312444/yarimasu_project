<?php
session_start(); // 세샨 시작

// 오류 출력 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그인 상태 확인 (로그인하지 않은 경우 로그인 페이지로 이동)
if (!isset($_SESSION['사용자아이디'])) {
    header("Location: login2.php");
    exit();
}

// DB 연결
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 현재 로그인한 사용자 아이디
$로그인한사용자 = $_SESSION['사용자아이디'];

// 페이지네이션 설정 - 이거는 잘 모르는 거라 지피티 한테서 가져옴
$게시글_페이지당_개수 = 10; // 한 페이지에 보여줄 게시글 수
$현재페이지 = isset($_GET['page']) ? intval($_GET['page']) : 1; // 현재 페이지 (기본값: 1)
$시작게시글번호 = ($현재페이지 - 1) * $게시글_페이지당_개수; // LIMIT의 시작 위치

// 전체 게시글 개수 가져오기
$total_sql = "SELECT COUNT(*) AS total FROM 게시판";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$전체게시글수 = $total_row['total']; // 총 게시글 개수

// 전체 페이지 수 계산
$전체페이지수 = ceil($전체게시글수 / $게시글_페이지당_개수);

// 현재 페이지에 해당하는 게시글 가져오기
$sql = "SELECT * FROM 게시판 ORDER BY id DESC LIMIT $시작게시글번호, $게시글_페이지당_개수";
$result = $conn->query($sql);

// 게시글 데이터를 배열에 저장
$게시글목록 = [];
while ($row = $result->fetch_assoc()) {
    $게시글목록[] = $row;
}

// 빈 칸을 채우기 위해 10칸이 될 때까지 빈 배열 추가 (마지막 페이지일 경우 빈칸 유지)
$빈칸수 = $게시글_페이지당_개수 - count($게시글목록);
for ($i = 0; $i < $빈칸수; $i++) {
    $게시글목록[] = null;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판</title>
    <style> /* 스타일 설정 */
/* 전체 페이지(body) 스타일 */
        body {
            text-align: center; /* 텍스트 중앙 정렬 */
            display: flex; /* Flexbox 레이아웃 적용 */
            flex-direction: column; /* 요소들을 세로 방향(컬럼)으로 배치 */
            align-items: center; /* 가로 방향으로 중앙 정렬 */
            justify-content: center; /* 세로 방향으로 중앙 정렬 */
            height: 100vh; /* 화면 높이의 100% 사용 */
            margin: 0; /* 기본 여백 제거 */
        }

        /* 게시판(board) 스타일 */
        #board {
            max-width: 90vw; /* 화면 너비의 90%까지 확장 */
            max-height: 80vh; /* 화면 높이의 80%까지 확장 */
            overflow-y: auto; /* 내용이 넘칠 경우 세로 스크롤 허용 */
            margin: 20px auto; /* 상하 여백 20px, 좌우 자동 정렬(가운데 정렬) */
            padding: 20px; /* 내부 여백 설정 */
            border: 1px solid #ccc; /* 연한 회색 테두리 적용 */
            box-sizing: border-box; /* 패딩을 포함한 크기 유지 */
            background-color: #f9f9f9; /* 배경색 설정 */
        }

        /* 테이블 스타일 */
        table {
            width: 100%; /* 테이블을 부모 요소 크기에 맞춤 */
            border-collapse: collapse; /* 테두리를 하나로 합쳐 깔끔하게 표시 */
            table-layout: fixed; /* 모든 열의 크기를 균등하게 유지 */
        }

        /* 테이블 헤더 및 데이터 셀 스타일 */
        th, td {
            border: 1px solid #ccc; /* 테이블 셀(칸)마다 연한 회색 테두리 추가 */
            padding: 10px; /* 셀 내부 여백 설정 */
            text-align: center; /* 텍스트를 중앙 정렬 */
            word-break: break-word; /* 긴 단어가 있으면 자동 줄바꿈 */
            width: 33.33%; /* 모든 칸의 너비를 동일하게 설정 */
            height: 50px; /* 모든 행(게시글 칸)의 높이를 고정 */
            vertical-align: middle; /* 셀 내부 텍스트를 수직 중앙 정렬 */
        }

        /* 페이지네이션 스타일 */
        .pagination {
            margin-top: 20px; /* 페이지네이션 상단 여백 추가 */
        }

        .pagination a {
            text-decoration: none; /* 링크의 밑줄 제거 */
            padding: 8px 12px; /* 내부 여백 설정 */
            margin: 2px; /* 요소 사이 여백 */
            border: 1px solid #ccc; /* 테두리 설정 */
            color: black; /* 기본 글씨 색상 */
            display: inline-block; /* 인라인 블록 요소로 배치 */
        }

        .pagination a.active {
            background-color: #007bff; /* 현재 페이지의 배경색을 파란색으로 설정 */
            color: white; /* 글자색을 흰색으로 변경 */
            border: 1px solid #007bff; /* 테두리 색상 일치 */
        }

        /* 로그아웃 버튼 스타일 */
        #logoutBtn {
            position: absolute; /* 절대 위치 지정 */
            top: 10px; /* 상단에서 10px 떨어진 위치 */
            right: 10px; /* 우측에서 10px 떨어진 위치 */
            background-color: red; /* 배경색을 빨간색으로 설정 */
            color: white; /* 글자색을 흰색으로 변경 */
            padding: 10px; /* 내부 여백 설정 */
            border: none; /* 테두리 제거 */
            cursor: pointer; /* 마우스 포인터 변경 */
        }
    </style>
</head>
<body>
    <h2>게시판</h2> <!-- 제목 설정 -->
    <p>안녕하세요, <strong><?= $로그인한사용자 ?></strong>님!</p> <!-- 현재 사용자가 누구인지 화면에 표시 -->
    <a href="logout2.php"><button id="logoutBtn">로그아웃</button></a> <!-- 로그아웃 버튼 생성 -->

    <div id="board"> <!-- 보드 생성 -->
        <h3>게시글 목록</h3> <!-- 게시판 이름 설정 -->
        <table> <!-- 테이블 생성 -->
            <thead> <!-- head랑 같은 역할 -->
                <tr> <!-- 행 설정 -->
                    <th>제목</th> <!-- 열 설정 -->
                    <th>작성자</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody> <!-- body랑 같은 역할 -->
                <?php foreach ($게시글목록 as $row): ?> <!-- 게시글 작성 -->
                    <tr>
                        <?php if ($row): ?> <!-- 제목 : 사용자 설정. 작성자 : 로그인한 아이디 자동 입력 -->
                            <td><?= htmlspecialchars($row['제목']) ?></td>
                            <td><?= htmlspecialchars($row['작성자']) ?></td>
                            <td> <!-- 수정 및 삭제 기능 구현 -->
                                <a href="post_edit.php?id=<?= $row['id'] ?>">수정</a> |
                                <a href="post_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
                            </td>
                        <?php else: ?> <!-- 화면에 빈 게시글 칸 생성 및 유지 -->
                            <td></td>
                            <td></td>
                            <td></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="pagination"> <!-- 페이지 만들고 싶어서 하였음 이거도 지피티한테 가져옴 -->
        <?php
        $페이지시작 = max(1, $현재페이지 - 2); // 현재 페이지 기준으로 5개씩 표시
        $페이지끝 = min($전체페이지수, $현재페이지 + 2);

        if ($현재페이지 > 1) {
            echo "<a href='main_page2.php?page=1'>처음</a>";
            echo "<a href='main_page2.php?page=" . ($현재페이지 - 1) . "'>이전</a>";
        }

        for ($i = $페이지시작; $i <= $페이지끝; $i++) {
            if ($i == $현재페이지) {
                echo "<a class='active'>$i</a>";
            } else {
                echo "<a href='main_page2.php?page=$i'>$i</a>";
            }
        }

        if ($현재페이지 < $전체페이지수) {
            echo "<a href='main_page2.php?page=" . ($현재페이지 + 1) . "'>다음</a>";
            echo "<a href='main_page2.php?page=$전체페이지수'>마지막</a>";
        }
        ?>
    </div>

    <a href="post_create.php"> <!-- 게시물 작성 php에 하이퍼링크 생성 -->
        <button>게시물 작성</button> <!-- 하이퍼링크 버튼 생성 -->
    </a>
</body>
</html>

<?php
$conn->close();
?>
