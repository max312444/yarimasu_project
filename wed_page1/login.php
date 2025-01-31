<?php
session_start(); // 세션 시작

// 오류 출력 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// MySQL 연결 정보
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// MySQL 연결 설정
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Fail to Connect. : " . $conn->connect_error);
}

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST 데이터 디버깅
    echo "<pre>";
    print_r($_POST); // POST 데이터 출력
    echo "</pre>";

    // Member_id 필드가 있는지 확인
    if (isset($_POST['Member_id']) && !empty($_POST['Member_id'])) {
        $Member_id = $_POST['Member_id'];

        // 회원번호 확인 쿼리
        $stmt = $conn->prepare("SELECT Name FROM won2 WHERE Member_id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $Member_id);
        $stmt->execute();
        $stmt->bind_result($Name);

        if ($stmt->fetch()) {
            // 로그인 성공: 세션 저장
            $_SESSION['user_id'] = $Member_id; // 회원번호를 세션에 저장
            $_SESSION['user_name'] = $Name; // 이름을 세션에 저장

            // 메인 페이지로 이동
            header("Location: main_page.php");
            exit; // 리다이렉트 후 코드 실행 중지
        } else {
            echo "<p style='color:red;'>Invalid membership number. Please try again.</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>Member_id is missing. Please check your input.</p>";
    }
}

$conn->close();
?>
