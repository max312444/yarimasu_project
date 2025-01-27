<?php

// 오류 출력 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 데이터베이스 연결 정보
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// 데이터베이스 연결 설정
$conn = new mysqli($host, $user, $password, $database);

// 연결 오류 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 사용자 입력 값 가져오기
    $Member_id = $_POST['Member_id'];
    $Name = $_POST['Name'];
    $Age = $_POST['Age'];
    $Gender = $_POST['Gender'];
    $Height = $_POST['Height'];

    // 데이터 삽입
    $stmt = $conn->prepare("INSERT INTO won2 (Member_id, Name, Age, Gender, Height) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiss", $Member_id, $Name, $Age, $Gender, $Height);

    if ($stmt->execute()) {
        echo "Sign up successful! You will be redirected to the login page in 3 seconds...";
        // 3초 후 리다이렉트
        header("refresh:3;url=login.html");
    } else {
        // Primary Key 중복 시 MySQL에서 반환하는 오류 처리
        if ($conn->errno === 1062) {
            echo "The member ID is already in use. Please use a different ID.";
        } else {
            echo "Sign up failed: " . $conn->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>
