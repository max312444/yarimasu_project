<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 회원가입 폼에서 전달된 값 받기
    $사용자아이디 = $_POST['사용자아이디'];
    $비밀번호 = password_hash($_POST['비밀번호'], PASSWORD_DEFAULT); // 비밀번호 해싱
    $이름 = $_POST['이름'];
    $성별 = $_POST['성별'];
    $생년월일 = $_POST['생년월일'];

    // 회원가입 INSERT 쿼리 실행
    $sql = "INSERT INTO jun_table (사용자아이디, 비밀번호, 이름, 성별, 생년월일) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql); // SQL과 연결 준비
    if (!$stmt) {
        die("SQL 준비 실패: " . $conn->error);
    } // 연결되면 테이블에 저장
    $stmt->bind_param("sssss", $사용자아이디, $비밀번호, $이름, $성별, $생년월일);

    if ($stmt->execute()) {
        // 회원가입 성공 시, 3초 후 로그인 페이지(login2.html)로 이동
        header("Refresh: 3; url=login2.html");
        echo "회원가입 성공! 3초 후 로그인 페이지로 이동합니다.";
        exit();
    } else {
        echo "회원가입 실패: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>
