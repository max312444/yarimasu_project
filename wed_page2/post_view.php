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

// 현재 로그인한 사용자가 작성자인지 확인
$작성자인지 = ($게시글['작성자'] === $로그인한사용자);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시물 보기</title>
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
        }

        /* 게시글 컨테이너 스타일 */
        .post-container {
            width: 50%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            text-align: left;
        }

        /* 버튼 스타일 */
        .button-container {
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .delete-btn {
            background-color: red;
            color: white;
        }

        .back-btn {
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <h2>게시물 상세 보기</h2>
    <div class="post-container">
        <h3><?= htmlspecialchars($게시글['제목']) ?></h3>
        <p><strong>작성자:</strong> <?= htmlspecialchars($게시글['작성자']) ?></p>
        <p><strong>작성 시간:</strong> <?= htmlspecialchars($게시글['작성시간']) ?></p>
        <hr>
        <p><?= nl2br(htmlspecialchars($게시글['내용'])) ?></p>

        <div class="button-container">
            <a href="main_page2.php"><button class="back-btn">목록으로 돌아가기</button></a>
            <?php if ($작성자인지): ?>
                <a href="post_edit.php?id=<?= $게시물ID ?>"><button class="edit-btn">수정</button></a>
                <a href="post_delete.php?id=<?= $게시물ID ?>"><button class="delete-btn">삭제</button></a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
