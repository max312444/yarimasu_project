<?php
session_start();

// 오류 출력 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB 연결 정보
$host = 'localhost'; # 연결 방법
$user = 'jo2'; # 사용자 이름
$password = '12'; # 비밀번호
$database = 'yarimasu'; # 데이터베이스

// MySQL 연결
$conn = new mysqli($host, $user, $password, $database); # 위에 입력한 정보들을 MySQl에 입력해서 접속함
if ($conn->connect_error) { # 입력한 정보가 맞지 않을 때
    die("DB 연결 실패: " . $conn->connect_error); 
}

// 로그인 처리
$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") { # 아이디 비밀번호 받는 부분
    $사용자아이디 = $_POST['사용자아이디'];
    $비밀번호 = $_POST['비밀번호'];

    // 아이디 검사 (SQL 문법 오류 수정)
    $sql = "SELECT * FROM 회원 WHERE 사용자아이디 = ?"; # 입력한 아이디가 테이블에 존재하는지 판별
    $stmt = $conn->prepare($sql);  # $stmt는 SQL 관련 작업을 하는 변수이다.
    $stmt->bind_param("s", $사용자아이디);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) { # 아이디 존재할 경우
        $row = $result->fetch_assoc();

        // 비밀번호 검증
        if (password_verify($비밀번호, $row['비밀번호'])) { # 비밀번호가 테이블에 존재하는지 판별
            $_SESSION['사용자아이디'] = $사용자아이디; # 입력한 아이디에 해당하는 비밀번호와 같은지 확인
            header("Location: main_page2.php"); # 로그인 성공 시 게시판으로 이동
            exit();
        } else { # 아이디에 해당하는 비밀번호가 아닐 경우
            $login_error = "비밀번호가 틀렸습니다.";
        }
    } else { # 아아디가 테이블에 없을 경우
        $login_error = "아이디가 존재하지 않습니다.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html> <!-- html 설계 -->
<html lang="ko"> <!-- 한글 인식 -->
<head>
    <meta charset="UTF-8"> <!-- 바이트 단위로 인코딩딩 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 출력할 화면 설정 사용자의 디스플레이의 크기에 맞춤춤 -->
    <title>로그인</title> <!-- 타이틀 설정 -->
    <style> /* css 입력 칸 및 글자 위치 조정 */
        body { 
            /* 전체 화면을 사용하여 중앙 정렬 */
            display: flex; /* Flexbox를 사용하여 요소 배치 */
            flex-direction: column; /* 요소를 세로 방향(컬럼)으로 정렬 */
            justify-content: center; /* 수직 방향(세로) 중앙 정렬 */
            align-items: center; /* 수평 방향(가로) 중앙 정렬 */
            height: 100vh; /* 화면 높이를 100%로 설정 (즉, 전체 화면 높이) */
            text-align: center; /* 내부 텍스트를 중앙 정렬 */
            margin: 0; /* body의 기본 마진 제거 */
        }

        form {
            /* 입력 폼을 세로 정렬로 배치 */
            display: flex; /* Flexbox를 사용하여 요소 배치 */
            flex-direction: column; /* 입력 요소를 세로 방향으로 정렬 */
            align-items: center; /* 폼 내부 요소를 중앙 정렬 */
            gap: 10px; /* 요소들 사이의 간격을 10px로 설정 */
        }

        button {
            /* 버튼 스타일 지정 */
            padding: 10px 20px; /* 내부 여백 설정 (위아래 10px, 좌우 20px) */
            font-size: 16px; /* 폰트 크기 설정 */
            cursor: pointer; /* 마우스 커서를 포인터(클릭 가능)로 변경 */
        }

        a {
            /* 링크 기본 스타일 제거 */
            text-decoration: none; /* 밑줄 제거 */
        }


        .error {
            color: red; /* 에러 색상 지정 */
        }
    </style>
</head>
<body>
    <h2>로그인</h2> <!-- 타이틀 -->

    <?php if ($login_error): ?> <!-- 로그인 에러 발생 -->
        <p class="error"><?= $login_error ?></p>
    <?php endif; ?>

    <form action="login2.php" method="POST"> <!-- 사용자가 입력하는 값을 login2.php로 전달해 로그인 기능 활성화 -->
        <input type="text" id="사용자아이디" name="사용자아이디" placeholder="아이디를 입력해주세요" required> <!-- 사용자 입력 -->
        <input type="password" id="비밀번호" name="비밀번호" placeholder="비밀번호를 입력해주세요" required> <!-- 사용자 입력 -->

        <button type="submit">로그인</button> <!-- 사용자가 입력을 마친 후 로그인 버튼을 누르면 폼 종료 -->
    </form>

    <p>회원이 아니신가요?</p> <!-- 회원이 아닌 경우 -->
    <a href="signup2.html"> <!-- 회원가입 페이지로 하이퍼링크 활성화 -->
        <button type="button">회원가입</button> <!-- 버튼 누르면 이동 -->
    </a>
</body>
</html>
