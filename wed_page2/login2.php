<?php
session_start();

// 오류 출력 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB 연결 정보
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// MySQL 연결
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 로그인 처리
$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $사용자아이디 = $_POST['사용자아이디'];
    $비밀번호 = $_POST['비밀번호'];

    // 아이디 검사 (SQL 문법 오류 수정)
    $sql = "SELECT * FROM 회원 WHERE 사용자아이디 = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $사용자아이디);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // 비밀번호 검증
        if (password_verify($비밀번호, $row['비밀번호'])) {
            $_SESSION['사용자아이디'] = $사용자아이디;
            header("Location: main_page2.php"); // 로그인 성공 시 게시판으로 이동
            exit();
        } else {
            $login_error = "비밀번호가 틀렸습니다.";
        }
    } else {
        $login_error = "아이디가 존재하지 않습니다.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            margin: 0;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        a {
            text-decoration: none;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>로그인</h2>

    <?php if ($login_error): ?>
        <p class="error"><?= $login_error ?></p>
    <?php endif; ?>

    <form action="login2.php" method="POST">
        <input type="text" id="사용자아이디" name="사용자아이디" placeholder="아이디를 입력해주세요" required>
        <input type="password" id="비밀번호" name="비밀번호" placeholder="비밀번호를 입력해주세요" required>

        <button type="submit">로그인</button>
    </form>

    <p>회원이 아니신가요?</p>
    <a href="signup2.html">
        <button type="button">회원가입</button>
    </a>
</body>
</html>
