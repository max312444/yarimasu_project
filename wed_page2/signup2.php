<?php
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// MySQL 연결
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 사용자 입력
$사용자아이디 = $_POST['사용자아이디'];
$비밀번호 = password_hash($_POST['비밀번호'], PASSWORD_DEFAULT); #비밀번호 해싱(암호화)
$이름 = $_POST['이름'];
$성별 = $_POST['성별'];
$생년월일 = $_POST['생년월일'];

// 회원가입 SQL
$sql = "INSERT INTO 회원 (사용자아이디, 비밀번호, 이름, 성별, 생년월일) VALUE (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $사용자아이디, $비밀번호, $이름, $성별, $생년월일);

if ($stmt->execute()) {
    echo "<script>alert('회원가입 성공! 로그인 페이지로 이동합니다.'); location.href='login2.php';</script>";
} else {
    echo "<script>alert('회원가입 실패! 다시 시도해주세요.'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>