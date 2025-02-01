<?php
session_start();

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

// 기존 게시글 정보 가져오기
$sql = "SELECT * FROM 게시판 WHERE id = ?"; // 게시판에서 아이디 가져옴
$stmt = $conn->prepare($sql); // SQL 작업 준비
$stmt->bind_param("i", $게시물ID);
$stmt->execute();
$result = $stmt->get_result();
$게시글 = $result->fetch_assoc();

// 게시물이 존재하지 않으면 오류 표시
if (!$게시글) {
    echo "게시글을 찾을 수 없습니다.";
    exit();
}

// 게시물 수정 처리 (POST 요청)
if ($_SERVER["REQUEST_METHOD"] === "POST") { // 입력한 정보로 테이블 내용 수정
    $새제목 = $_POST['제목'];
    $새내용 = $_POST['내용'];

    // 게시글 업데이트
    $update_sql = "UPDATE 게시판 SET 제목 = ?, 내용 = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $새제목, $새내용, $게시물ID);

    if ($update_stmt->execute()) {
        echo "<script>alert('게시물이 수정되었습니다.'); location.href='main_page2.php';</script>";
    } else {
        echo "<script>alert('게시물 수정 실패! 다시 시도해주세요.'); history.back();</script>";
    }
    exit();
} 
    // 단점 : 수정하면 제일 위로 올라가야 하는데 이건 너무 어려워서 아직 패스...
    // 그리고 자바스크립트 사용해서 이건 다시 수정할 예정정

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko"> <!-- 한글 인식용 -->
<head>
    <meta charset="UTF-8"> <!--  byte로 인코딩 -->
    <title>게시물 수정</title> <!-- 제목 설정 -->
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

        /* 폼 스타일 */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 50%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        /* 입력 필드 스타일 */
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* 버튼 스타일 */
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
        }

        /* 취소 버튼 스타일 */         .cancel-btn {
            background-color: #ccc;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>게시물 수정</h2> <!-- 제목 설정 -->
    <form action="post_edit.php?id=<?= $게시물ID ?>" method="POST">
        <input type="text" name="제목" value="<?= htmlspecialchars($게시글['제목']) ?>" required>
        <textarea name="내용" rows="10" required><?= htmlspecialchars($게시글['내용']) ?></textarea>
        <button type="submit">수정 완료</button> <!-- 수정이 완료되면 이곳을 눌러 데이터를 테이블에 저장 -->
    </form>
    <a href="main_page2.php"><button class="cancel-btn">취소</button></a> <!-- 메인 페이지로 가는 하이퍼링크 생성 및 버튼 클릭 시 이동 -->
</body>
</html>
