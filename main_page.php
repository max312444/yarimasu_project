<?php
session_start(); // 세션 시작

// 오류 출력 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// MySQL 연결 정보
$host = 'localhost';
$user = 'jo2';
$password = '12';
$database = 'yarimasu';

// MySQL 연결 설정
$conn = new mysqli($host, $user, $password, $database);

// MySQL 문자셋 설정
$conn->set_charset("utf8mb4"); // 문자셋 설정

// 연결 오류 확인
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// 세션에서 로그인된 사용자 ID 가져오기
if (isset($_SESSION['user_id'])) {
    $Member_id = $_SESSION['user_id']; // 세션에서 회원번호 가져오기

    // SQL 쿼리 작성
    $query = "SELECT Member_id, Name, Age, Gender, Height FROM won2 WHERE Member_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error); // 준비 실패시 오류 메시지 출력
    }

    $stmt->bind_param("i", $Member_id); // Member_id를 정수형으로 바인딩
    $stmt->execute();
    
    // 결과 바인딩 (반환하는 컬럼의 수와 변수 수가 일치해야 합니다)
    $stmt->bind_result($Member_id, $Name, $Age, $Gender, $Height); // 5개의 컬럼에 맞춰 바인딩

    // 결과 확인
    if ($stmt->fetch()) {
        // 회원 정보 출력
        $user_info = [
            'Name' => $Name,
            'Age' => $Age,
            'Gender' => $Gender,
            'Height' => $Height
        ];
    } else {
        echo "No user found with Member_id = " . $Member_id;
        exit;
    }

    $stmt->close();
} else {
    echo "User is not logged in.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        .main-container {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 20px;
            padding: 20px;
        }
        .block {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #ddd;
            background-color: white;
            padding: 20px;
            height: 600px; /* 모든 블록의 높이를 600px로 고정 */
            box-sizing: border-box;
        }
        .block h3 {
            margin-bottom: 20px;
        }
        .block.gray {
            background-color: #ccc; /* 회색 배경 */
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: red;
        }
    </style>
    <script>
        // 로그아웃 확인 창
        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'logout.php'; // 로그아웃 처리 페이지로 이동
            } else {
                alert('Logout has been cancelled.');
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Main Page</h1>
    </header>
    <div class="main-container">
        <!-- 왼쪽: 관절 인식 사진 -->
        <div class="block gray">
            <h3>Joint recognition picture</h3>
            <div style="width: 150px; height: 150px; background-color: gray;"></div>
        </div>

        <!-- 가운데: 날짜별 사진 -->
        <div class="block">
            <h3>Pictures by date</h3>
            <p>Photo feature is under development.</p>
        </div>

        <!-- 오른쪽: 회원 정보 -->
        <div class="block">
            <h3>Member information</h3>
            <p>Member ID: <?= htmlspecialchars($Member_id) ?></p>
            <p>Name: <?= htmlspecialchars($user_info['Name']) ?></p>
            <p>Age: <?= htmlspecialchars($user_info['Age']) ?></p>
            <p>Male or Female: <?= htmlspecialchars($user_info['Gender']) ?></p>
            <p>Height: <?= htmlspecialchars($user_info['Height']) ?> cm</p>
            <button onclick="window.location.href='edit_profile.php'">Change information</button>
            <button onclick="if(confirm('Are you sure you want to leave?')) alert('Membership has been withdrawn.');">Membership Withdrawal</button>
            <button class="logout-btn" onclick="confirmLogout()">Logout</button>
        </div>
    </div>
</body>
</html>
