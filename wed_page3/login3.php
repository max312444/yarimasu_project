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
# 연결 실패 시 에러 메시지 출력 후 종료
if($conn->connect_error) {
    die('DB 연결 실패'. $conn->connect_error);
}

// 로그인 요청이 POST 방식으로 왔는지 확인
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    # 아이디와 비밀번호가 입력되었는지 확인
    if (isset($_POST['사용자아이디']) && isset($_POST['비밀번호'])) {
        $사용자아이디 = trim($_POST['사용자아이디']); # 아이디 앞뒤 공백 제거
        $비밀번호 = trim($_POST['비밀번호']); # 비밀번호 앞뒤 공백 제거

        // 아이디 검사
        $sql = "SELECT * FROM jun_table WHERE 사용자아이디 = ?"; // 테이블에 존재하는지 확인
        $stmt = $conn->prepare($sql); // SQL 실행을 위한 준비
        $stmt->bind_param("s", $사용자아이디); # SQL 인젝션 방지를 위해 바인딩 (문자열 타입)
        $stmt->execute(); // SQL 실행
        $result = $stmt->get_result(); // 실행 결과 가져옴

        if ($result->num_rows > 0) { // 아이디 테이블에 존재 함
            $_SESSION['사용자아이디'] = $사용자아이디; // 세션에 사용자 아이디 저장 (로그인 유지)
            header("Location: main_page3.php"); # 로그인 성공시 메인 페이지로 이동
            exit(); # 이동하면 코드 종료
        } else { // 아이디와 비번이 안맞는 경우우
            $login_error = "비밀번호가 맞지 않습니다.";
        }
    } else { # 아이디가 안맞을 때
        $login_error = "아이디가 맞지 않습니다.";
    }
    $stmt->close(); // SQL 끝난 뒤 객체 종료
}
$conn->close(); // 연결 종료
?>

