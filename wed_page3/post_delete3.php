<?php
    session_start();
    # 로그인 정보 확인
    if (!isset($_SESSION['사용자아이디'])) {
        header("Location: login3.php");
        exit();
    }

    # DB 연결 정보 확인
    $host = 'localhost';
    $user = 'jo2';
    $password = '12';
    $database = 'yarimasu';

    # MySQL 연결
    $conn = new mysqli($host, $user, $password, $database);
    if ($conn->connect_error) {
        die('DB 연결 실패'. $conn->connect_error);
    }

    # GET 요청으로 받은 게시물 ID 확인
    if (!isset($_GET['id'])) {
        echo"삭제할 게시물의 ID가 제공되지 않았습니다.";
        exit();
    }


    $post_id = intval($_GET['id']);
    $로그인한사용자 = $_SESSION['사용자아이디'];
    $isAdmin = ($로그인한사용자 === 'admin');

    # 관리자나 작성자인지 확인
    $sql = "SELECT 작성자 FROM 게시판 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    # 관리자나 작성자면 삭제 가능
    if ($isAdmin || $row["작성자"] === $로그인한사용자) {
        $sql = "DELETE FROM 게시판 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL 준비 실패" . $conn->error);
        }
        $stmt->bind_param("i", $post_id);
        if ($stmt->execute()) { # 삭제 성공시 main_page3.php로 이동
            header("Location: main_page3.php");
            exit();
        } else {
            echo"게시물 삭제 실패! 다시 시도해주세요.";
        }
    } else { # 삭제 기능 없는 경우 삭제 불가 메시지 출력
        echo "자신이 작성한 게시물만 삭제할 수 있습니다. 3초 뒤 메인으로 돌아갑니다.";
        header("Refresh: 3; url=main_page3.php");
        exit();
    }
$stmt->close();
$conn->close();
?>