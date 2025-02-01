<?php
session_start(); // 세션 시작

// 로그인 상태 확인 (로그인하지 않은 경우 로그인 페이지로 이동)
if (!isset($_SESSION['사용자아이디'])) {
    header("Location: login2.php");
    exit();
}

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

// GET 요청으로 받은 게시물 ID 확인
if (!isset($_GET['id'])) {
    echo "삭제할 게시물의 ID가 제공되지 않았습니다.";
    exit();
}

$post_id = intval($_GET['id']); // 게시물 ID 가져오기
$로그인한사용자 = $_SESSION['사용자아이디']; // 현재 로그인한 사용자

// 게시물 작성자 확인
$sql = "SELECT 작성자 FROM 게시판 WHERE id = ?"; // 게시판 테이블에서 작성자 이름 확인
$stmt = $conn->prepare($sql); // SQL 작업 준비
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// // 게시글이 존재하지 않는 경우
// if (!$row) {
//     echo "해당 게시글을 찾을 수 없습니다.";
//     exit();
// }

// 작성자가 로그인한 사용자인지 확인
if ($row['작성자'] !== $로그인한사용자) { // 작성자랑 아이디랑 맞지 않으면 삭제 X
    echo "자신이 작성한 게시물만 삭제할 수 있습니다. 3초뒤 메인으로 돌아갑니다.";
    header("Refresh: 3; url=main_page2.php"); // 3초의 시간을 두고 메인 홈으로 이동
    exit();
}

// 작성자가 맞으면 게시물 삭제 진행
$sql = "DELETE FROM 게시판 WHERE id = ?"; // 작성자와 아이디가 맞으면 삭제 진행
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL 준비 실패: " . $conn->error);
}

$stmt->bind_param("i", $post_id);
if ($stmt->execute()) {
    // 삭제 성공 시 게시판 목록으로 이동
    header("Location: main_page2.php"); // 헤더로 메인 페이지로 이동
    exit();
} else {
    echo "게시물 삭제 실패! 다시 시도해주세요.";
}

$stmt->close();
$conn->close();
?>
