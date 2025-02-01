<?php
session_start(); // 세션 시작

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

if ($_SERVER["REQUEST_METHOD"] == "POST") { // 사용자의 요청 방식이 POST인지 확인
    if (isset($_POST['사용자아이디']) && isset($_POST['비밀번호'])) { // 아이디 비밀번호가 전송 됬는지 확인
        $사용자아이디 = trim($_POST['사용자아이디']); // 전송 받은 아이디의 앞뒤 공백제거
        $비밀번호 = trim($_POST['비밀번호']); // 전송 받은 비밀번호호의 앞뒤 공백제거

        // 아이디 검사
        $sql = "SELECT * FROM jun_table WHERE 사용자아이디 = ?"; // SQL 테이블에 있는지 확인
        $stmt = $conn->prepare($sql); // $stmt는 SQL문을 실행할 준비를 하는 객체이다. prepare()매서드로 생성됨됨
        $stmt->bind_param("s", $사용자아이디); // 사용자 아이디 입력 하는 부분
        $stmt->execute(); // 실행
        $result = $stmt->get_result(); // 결과 가져옴

        if ($result->num_rows > 0) { // 아이디가 SQL 테이블에 있는 경우
            $row = $result->fetch_assoc();

            // 비밀번호 검증
            if (password_verify($비밀번호, $row['비밀번호'])) { 
                $_SESSION['사용자아이디'] = $사용자아이디; // 입력한 아이디에 해당하는 비밀번호인지 확인
                header("Location: main_page2.php"); //  로그인 성공하면 게시판으로 이동
                exit();
            } else { // 아이디와 맞지 않을 경우
                $login_error = "비밀번호가 틀렸습니다.";
            }
        } else { // 아이디가 SQL 테이블에 없는 경우
            $login_error = "아이디가 존재하지 않습니다.";
        }

        $stmt->close(); //  SQL 실행 끝난뒤에 객체 종료
    }
}

$conn->close(); // 연결 종료
?>