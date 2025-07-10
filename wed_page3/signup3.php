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

   // 중복 검사 추가
   $check_sql = "SELECT * FROM jun_table WHERE 사용자아이디 = ?";  # 테이블에서 입력값 있는지 확인
   $check_stmt = $conn->prepare($check_sql); # SQL 작업 준비
   $check_stmt->bind_param("s", $사용자아이디); # 문자형으로 바인딩
   $check_stmt->execute(); # 실행
   $check_result = $check_stmt->get_result(); # 실행 결과 가져오기

   if ($check_result->num_rows > 0) {
       // 이미 존재하는 아이디일 경우
       echo "이미 등록된 아이디입니다. 다른 아이디를 사용해주세요.";
       $check_stmt->close(); # stmt 종료
       $conn->close(); # conn 종료
       header("Refresh: 3; url=signup3.html"); # 회원가입으로 다시 돌아가기
       exit(); // 실행 종료
   }
   $check_stmt->close(); // 중복 검사 종료

   // 기존 회원가입 코드
   $sql = "INSERT INTO jun_table (사용자아이디, 비밀번호, 이름, 성별, 생년월일) VALUES (?, ?, ?, ?, ?)"; # 입력한 값 테이블에 저장
   $stmt = $conn->prepare($sql); // 연결 작업 준비
   if (!$stmt) { 
    die("SQL 준비 실패: ". $conn->error);
   }// 연결되면 저장
   $stmt->bind_param("sssss", $사용자아이디, $비밀번호, $이름, $성별, $생년월일); # 문자형으로 바인딩

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
