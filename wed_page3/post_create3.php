<?php
    # 세션 시작
    session_start();

    # 로그인 여부 확인
    if (!isset($_SESSION['사용자아이디'])) {
        header('Location: login3.html');
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

    # 테이블에 입력할 정보 기입
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $제목 = $_POST['제목'];
        $내용 = $_POST['내용'];
        $작성자 = $_SESSION['사용자아이디']; # 작성자는 로그인한 사람으로
        
        # SQL 쿼리
        $sql = "INSERT INTO 게시판 (제목, 작성자, 내용) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL 준비 실패". $conn->error);
        }
        $stmt->bind_param("sss", $제목, $작성자, $내용);
        # 게시물 등록 성공시 3초뒤에 메인 페이지로 이동
        if ($stmt->execute()) {
            header("Refresh:3; url=main_page3.php");
            echo "게시물이 등록되었습니다. 3초 후 메인 페이지로 이동합니다.";
            exit();
        } else {
            echo"등록 실패!!";
        }
        $stmt->close();
    }
    $conn->close();
?> 
<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <title>게시물 작성</title>
    </head>
    <body>
        <h2>게시물 작성</h2>
        <div class="post-form">
            <form action="post_create.php" method="POST">
                <input type="text" name="제목" placeholder="제목 입력" required><br>
                <textarea name="내용" placeholder="내용 입력" required></textarea><br>
                <button type="submit">등록</button>
            </form>
            <a href="main_page3.php"><button class="cancel-btn">작성취소</button></a>
        </div>
    </body>
</html>