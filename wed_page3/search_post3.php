<?php
// 이 코드는 main_page3.php 에서 검색 기능을 따로 분리한 코드이다.
// DB 연결 정보 
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// DB 연결
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die('DB 연결 실패: ' . $conn->connect_error);
}

// 🔹 검색어 가져오기
$검색어 = isset($_GET['search']) ? trim($_GET['search']) : '';

// 🔹 SQL 문 생성 (검색어가 있으면 필터 적용)
$sql = "SELECT * FROM 게시판";
if (!empty($검색어)) {
    $sql .= " WHERE 제목 LIKE '%$검색어%' OR 작성자 LIKE '%$검색어%'";
}
$sql .= " ORDER BY 작성시간 DESC";

// 🔹 검색 결과 실행
$result = $conn->query($sql);
$검색결과목록 = [];
while ($row = $result->fetch_assoc()) {
    $검색결과목록[] = $row;
}

$conn->close();
?>
