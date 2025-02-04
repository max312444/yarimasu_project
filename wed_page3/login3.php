<?php
// 세션 시작
session_start();

// DB 연결 정보 받기 
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// MySQL 연결
$conn = new mysqli($host, $user, $password, $database);
if($conn->connect_error) {
    die('DB 연결 실패'. $conn->connect_error);
}

// 로그인 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['사용자아이디']) && isset($_POST['비밀번호'])) {
        $사용자아이디 = trim($_POST['사용자아이디']);
        $비밀번호 = trim($_POST['비밀번호']);

        // 아이디 검사
        $sql = "SELECT * FROM jun_table WHERE 사용자아이디 = ?"; // 테이블에 존재하는지 확인
        $stmt = $conn->prepare($sql); // SQL 준비
        $stmt->bind_param("s", $사용자아이디);
        $stmt->execute(); // 실행
        $result = $stmt->get_result(); // 결과 가져옴

        if ($result->num_rows > 0) { // 아이디 테이블에 존재
            $_SESSION['사용자아이디'] = $사용자아이디; // 비밀번호 확인
            header("Location: main_page3.php");
            exit();
        } else { // 아이디와 비번이 안맞음
            $login_error = "비밀번호가 맞지 않습니다.";
        }
    } else {
        $login_error = "아이디가 맞지 않습니다.";
    }
    $stmt->close(); // SQL 끝난 뒤 객체 종료료
}
$conn->close(); // 연결 종료
?>

