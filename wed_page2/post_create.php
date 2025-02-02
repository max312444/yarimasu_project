<?php
session_start(); // 세션 시작

// 오류 출력 활성화 (개발 시)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그인 여부 확인
if (!isset($_SESSION['사용자아이디'])) {
    header("Location: login2.php");
    exit();
}

// DB 연결 정보
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// DB 연결
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // 테이블에 데이터 전송 확인
    $제목 = $_POST['제목'];
    $내용 = $_POST['내용'];

    // 작성자는 로그인한 사용자의 아이디로 자동 설정
    $작성자 = $_SESSION['사용자아이디'];
    
    // // 공지 여부는 기본값 0으로 저장 (나중에 수정 가능)
    // $공지 = 0;
    
    // SQL 쿼리: 제목, 작성자, 내용, 공지
    $sql = "INSERT INTO 게시판 (제목, 작성자, 내용) VALUES (?, ?, ?)"; // 이곳도 공지를 뺏음
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL 준비 실패: " . $conn->error);
    }
    
    // 변수 순서는 쿼리의 순서와 일치해야함
    $stmt->bind_param("sss", $제목, $작성자, $내용); // 원래는 공지도 넣어놨었는데 안쓸꺼같아서 뺏음

    if ($stmt->execute()) {
        // 게시물 등록 성공 시, 3초 후 메인 페이지로 리다이렉트
        header("Refresh: 3; url=main_page2.php"); // 헤더를 사용해서 main_page2.php로 이동함
        echo "게시물이 등록되었습니다. 3초 후 게시판으로 이동합니다.";
        exit();
    } else {
        echo "등록 실패!";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시물 작성</title> <!-- 제목 설정 -->
    <style>
        /* 전체 화면을 중앙 정렬 */
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4; /* 부드러운 배경색 추가 */
        }

        /* 게시글 작성 폼 스타일 */
        .post-form {
            width: 500px; /* 폼 크기 설정 */
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* 제목 입력 필드 */
        .post-form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* 내용 입력 필드 */
        .post-form textarea {
            width: 100%;
            height: 200px; /* 높이를 넉넉하게 설정 */
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none; /* 사용자가 크기 조절 못하게 */
        }

        /* 등록 버튼 */
        .post-form button {
            width: 100%;
            padding: 10px;
            font-size: 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        /* 버튼 호버 효과 */
        .post-form button:hover {
            background-color: #0056b3;
        }
        
        /* 작성 취소 버튼 스타일 */
        .cancel-btn {
            width: 100%;
            padding: 10px;
            font-size: 18px;
            background-color: #ccc;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        /* 버튼 호버 효과 */
        .cancel-btn:hover {
            background-color: #999;
        }

    </style>
</head>
<body>
    <h2>게시물 작성</h2> <!-- 제목 설정 -->
    
    <div class="post-form"> <!-- 폼을 감싸는 박스 -->
        <form action="post_create.php" method="POST">
            <input type="text" name="제목" placeholder="제목 입력" required><br>
            <textarea name="내용" placeholder="내용 입력" required></textarea><br>
            <button type="submit">등록</button> <!-- 등록 버튼 생성 -->
        </form>
        <a href="main_page2.php"><button class="cancel-btn">작성 취소</button></a> <!-- 작성 취소 버튼 -->
    </div>
</body>
</html>
