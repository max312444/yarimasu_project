<?php
    # 세션 시작
    session_start();

    # 로그인 상태 확인
    if (!isset($_SESSION["사용자아이디"])) {
        header("Location: login3.php");
    }

    # DB 연결 정보
    $host = 'localhost';
    $user = 'jo2';
    $password = '12';
    $database = 'yarimasu';

    # DB 연결
    $conn = new mysqli($host, $user, $password, $database);
    if ($conn->connect_error) {
        die('DB 연결 실패'. $conn->connect_error);
    }

    # ID 확인
    if (!isset($_GET['id'])) {
        echo"잘못된 접근입니다.";
        exit();
    }

    # 기존 게시글 정보 가져오기
    $sql = "SELECT * FROM 게시판 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $게시물ID);
    $stmt->execute();
    $result  = $stmt->get_result();
    $게시글 = $result->fetch_assoc();

    # 게시글이 없으면 오류 표시
    if (!$게시물) {
        echo"게시물을 찾을 수 없습니다.";
        exit();
    }

    # 현재 로그인된 사용자가 작성자인지 확인
    $작성자인지 = ($게시글['작성자'] === $로그인한사용자);

    $conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <title>게시물 보기</title>
    </head>
    <body>
        <h2>게시물 상세 보기</h2>
        <div class="post-container">
            <h3><?= htmlspecialchars($게시글['제목']) ?></h3>
            <p><strong>작성자:</strong> <?= htmlspecialchars($게시글['작성자']) ?></p>
            <p><strong>작성 시간:</strong><?= htmlspecialchars($게시글['작성시간']) ?></p>
            <hr>
            <p><?= nl2br(htmlspecialchars($게시글['내용'])) ?></p>
            <div class="button-container">
                <a href="main_page3.php"><button class="back-btn">목록으로 돌아가기</button></a>
                <?php if ($작성자인지): ?>
                    <a href="post_edit3.php?id<?= $게시물ID ?>"><button class="edit-btn">수정</button></a>
                    <a href="post_delete3.php?id<?= $게시물ID ?>"><button class="delete-btn">삭제</button></a>
                    <?php endif; ?>
            </div>
        </div>
    </body>
</html>