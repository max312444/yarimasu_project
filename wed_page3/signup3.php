<?php
// DB 연결 정보
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu'; 

// MySQL 연결
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("DB 연결 실패: ". $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // 회원가입 정보 받기 
   $사용자아이디 = $_POST['사용자아이디'];
   $비밀번호 = $_POST['비밀번호'];
   $이름 = $_POST['이름'];
   $성별 = $_POST['성별'];
   $생년월일 = $_POST['생년월일'];

   $sql = "INSERT INTO jun_table (사용자아이디, 비밀번호, 이름, 성별, 생년월일) VALUES (?, ?, ?, ?, ?)";
   $stmt = $conn->prepare($sql); // 연결 작업 준비
   if (!$stmt) {
    die("SQL 준비 실패: ". $conn->error);
   }// 연결되면 저장
   $stmt->bind_param("sssss", $사용자아이디, $비밀번호, $이름, $성별, $생년월일);

   if ($stmt->execute()) { // 성공하면 로그인 화면으로 3초 뒤에 이동
    header("Refresh: 3; url=login3.html");
    echo "회원가입 성공! 3초 뒤에 로그인 화면으로 이동합니다.";
    exit();
   } else {
    echo "회원가입 실패 : ". $conn->error;
   }
   $stmt->close();
}
$conn->close();
?>