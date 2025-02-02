<?php
session_start(); // 세션 시작

// 오류 출력 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그인 상태 확인 (로그인하지 않은 경우 로그인 페이지로 이동)
if (!isset($_SESSION['사용자아이디'])) {
    header("Location: login2.php");
    exit();
}

// DB 연결
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// GET 요청을 통해 게시물 ID를 받음
if (!isset($_GET['id'])) {
    echo "잘못된 접근입니다.";
    exit();
}

$게시물ID = intval($_GET['id']); // ID 값 정수 변환
$로그인한사용자 = $_SESSION['사용자아이디']; // 현재 로그인한 사용자
$isAdmin = ($로그인한사용자 === 'admin'); // ✅ 관리자 계정 여부 확인

// 기존 게시글 정보 가져오기
$sql = "SELECT * FROM 게시판 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $게시물ID);
$stmt->execute();
$result = $stmt->get_result();
$게시글 = $result->fetch_assoc();

// 게시물이 존재하지 않으면 오류 표시
if (!$게시글) {
    echo "게시글을 찾을 수 없습니다.";
    exit();
}

// ✅ 관리자이거나 작성자가 로그인한 사용자와 같은지 확인
if (!$isAdmin && $게시글['작성자'] !== $로그인한사용자) {
    echo "수정 권한이 없습니다. 메인으로 이동합니다.";
    header("Refresh: 3; url=main_page2.php");
    exit();
}

// 게시물 수정 처리 (POST 요청)
if ($_SERVER["REQUEST_METHOD"] === "POST") { 
    $새제목 = $_POST['제목'];
    $새내용 = $_POST['내용'];

    // 게시글 업데이트 (수정하면 `작성시간` 업데이트)
    $update_sql = "UPDATE 게시판 SET 제목 = ?, 내용 = ?, 작성시간 = NOW() WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $새제목, $새내용, $게시물ID);

    if ($update_stmt->execute()) {
        // 수정 완료 후 메인 페이지로 이동
        header("Location: main_page2.php");
        exit();
    } else {
        echo "게시물 수정 실패! 다시 시도해주세요.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시물 수정</title>
    <style>
        /* 전체 페이지 스타일 */
        body {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        /* 수정 폼 스타일 */
        .edit-form {
            width: 500px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* 입력 필드 스타일 */
        .edit-form input[type="text"],
        .edit-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* 내용 입력 필드 크기 조절 */
        .edit-form textarea {
            height: 200px;
            resize: none;
        }

        /* 버튼 스타일 */
        .edit-form button {
            width: 100%;
            padding: 10px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* 취소 버튼 스타일 */
        .cancel-btn {
            background-color: #ccc;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>게시물 수정</h2>
    
    <div class="edit-form">
        <form action="post_edit.php?id=<?= $게시물ID ?>" method="POST">
            <input type="text" name="제목" value="<?= htmlspecialchars($게시글['제목']) ?>" required>
            <textarea name="내용" required><?= htmlspecialchars($게시글['내용']) ?></textarea>
            <button type="submit">수정 완료</button>
        </form>
        <a href="main_page2.php"><button class="cancel-btn">취소</button></a>
    </div>
</body>
</html>
