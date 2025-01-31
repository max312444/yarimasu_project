<?php
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// DB 연결
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $제목 = $_POST['제목'];
    $내용 = $_POST['내용'];
    $작성자 = $_POST['작성자'];

    $sql = "INSERT INTO 게시판 (제목, 내용, 작성자, 공지) VALUES (?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $제목, $내용, $작성자);

    if ($stmt->execute()) {
        echo "<script>alert('게시물이 등록되었습니다.'); location.href='main_page2.php';</script>";
    } else {
        echo "<script>alert('등록 실패!'); history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시물 작성</title>
</head>
<body>
    <h2>게시물 작성</h2>
    <form action="post_create.php" method="POST">
        <input type="text" name="제목" placeholder="제목 입력" required><br>
        <textarea name="내용" placeholder="내용 입력" required></textarea><br>
        <input type="text" name="작성자" placeholder="작성자 입력" required><br>
        <button type="submit">등록</button>
    </form>
</body>
</html>
