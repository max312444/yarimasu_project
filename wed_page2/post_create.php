<?php
session_start(); // 세션 시작

// 오류 출력 활성화 (개발 시)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그인 여부 확인
if (!isset($_SESSION['사용자아이디'])) {
    header("Location: login2.php");
    exit();
}

// DB 연결 정보
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// DB 연결
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // 테이블에 데이터 전송 확인
    $제목 = $_POST['제목'];
    $내용 = $_POST['내용'];

    // 작성자는 로그인한 사용자의 아이디로 자동 설정
    $작성자 = $_SESSION['사용자아이디'];
    
    // // 공지 여부는 기본값 0으로 저장 (나중에 수정 가능)
    // $공지 = 0;
    
    // SQL 쿼리: 제목, 작성자, 내용, 공지
    $sql = "INSERT INTO 게시판 (제목, 작성자, 내용) VALUES (?, ?, ?)"; // 이곳도 공지를 뺏음
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL 준비 실패: " . $conn->error);
    }
    
    // 변수 순서는 쿼리의 순서와 일치해야함
    $stmt->bind_param("sss", $제목, $작성자, $내용); // 원래는 공지도 넣어놨었는데 안쓸꺼같아서 뺏음

    if ($stmt->execute()) {
        // 게시물 등록 성공 시, 3초 후 메인 페이지로 리다이렉트
        header("Refresh: 3; url=main_page2.php"); // 헤더를 사용해서 main_page2.php로 이동함
        echo "게시물이 등록되었습니다. 3초 후 게시판으로 이동합니다.";
        exit();
    } else {
        echo "등록 실패!";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시물 작성</title> <!-- 제목 설정 -->
</head>
<body>
    <h2>게시물 작성</h2> <!-- 제목 설정 -->
    <form action="post_create.php" method="POST">
        <input type="text" name="제목" placeholder="제목 입력" required><br>
        <textarea name="내용" placeholder="내용 입력" required></textarea><br>
        <!-- 로그인한 사용자 아이디가 작성자가 됨. -->
        <button type="submit">등록</button> <!-- 등록 버튼 생성 -->
    </form>
</body>
</html>
