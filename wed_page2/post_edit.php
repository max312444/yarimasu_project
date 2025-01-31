<?php
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

$conn = new mysqli($host, $user, $password, $database);

$id = $_GET['id'];
$sql = "SELECT * FROM 게시판 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $제목 = $_POST['제목'];
    $내용 = $_POST['내용'];

    $update_sql = "UPDATE 게시판 SET 제목=?, 내용=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $제목, $내용, $id);

    if ($update_stmt->execute()) {
        echo "<script>alert('게시물이 수정되었습니다.'); location.href='main_page2.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시물 수정</title>
</head>
<body>
    <h2>게시물 수정</h2>
    <form action="post_edit.php?id=<?= $id ?>" method="POST">
        <input type="text" name="제목" value="<?= $row['제목'] ?>" required><br>
        <textarea name="내용" required><?= $row['내용'] ?></textarea><br>
        <button type="submit">수정</button>
    </form>
</body>
</html>
