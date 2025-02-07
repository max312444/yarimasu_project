<?php
    # 세션 시작
    session_start();

    # 로그인 상태 확인
    if (!isset($_SESSION['사용자아이디'])) {
        header('Location: login3.php');
        exit();
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

    # ID 정보 받음
    if (!isset($_GET['id'])) {
        echo'잘못된 접근입니다.';
        exit();
    }

     $게시물ID = intval($_GET['id']);
     $로그인한사용자 = $_SESSION['사용자아이디'];
     $isAdmin = ($로그인한사용자 === 'admin');

    # 기존 게시글 가져오기
    $sql = "SELECT * FROM 게시판 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $게시물ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $게시글 = $result->fetch_assoc();

    # 게시글이 없으면 오류 표시
    if (!$게시글) {
        echo("게시글을 찾을 수 없습니다.");
        exit();
    }

    # 관리자인지 작성자 본인인지 확인
    if (!$isAdmin && $게시글['작성자'] !== $로그인한사용자) {
        echo("수정 권한이 없습니다. 메인으로 이동합니다.");
        exit();
    }

    # 게시글 수정
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $새제목 = $_POST['제목'];
        $새내용 = $_POST['내용'];

        # 게시글 업데이트 - 작성시간도 업데이트
        $update_sql = "UPDATE 게시판 SET 제목 = ?, 내용 = ?, 작성시간 = NOW() WHERE id =?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $새제목, $새내용, $게시물ID);

        if ($update_stmt->execute()) {# 수정 완료되면 메인 페이지로 이동
            header("Location: main_page3.php");
            exit();
        } else {
            echo"게시물 수정 실패! 다시 시도해주세요.";
        }
    }
$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <title>게시물 수정</title>
    </head>
    <body>
        <h2>게시물 수정</h2>

        <div class="edit-form">
            <form action="post_edit3.php?id=<?= $게시물ID ?>" method="POST">
                <input type="text" name="제목" value="<?= htmlspecialchars($게시글['제목']) ?>" required>
                <textarea name="내용"required><?= htmlspecialchars($게시글['내용']) ?></textarea>
                <button type="submit">수정 완료</button>
            </form>
        </div>
    </body>
</html>