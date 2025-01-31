<?php
session_start();

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

// 현재 로그인한 사용자 아이디
$로그인한사용자 = $_SESSION['사용자아이디'];

// 게시글 목록 가져오기
$sql = "SELECT * FROM 게시판 ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시판</title>
    <style>
        body {
            text-align: center;
        }

        #board {
            width: 60%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        #logoutBtn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>게시판</h2>
    <p>안녕하세요, <strong><?= $로그인한사용자 ?></strong>님!</p>
    <a href="logout.php"><button id="logoutBtn">로그아웃</button></a>

    <div id="board">
        <h3>게시글 목록</h3>
        <table>
            <thead>
                <tr>
                    <th>제목</th>
                    <th>작성자</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['제목']) ?></td>
                        <td><?= htmlspecialchars($row['작성자']) ?></td>
                        <td>
                            <a href="post_edit.php?id=<?= $row['id'] ?>">수정</a> |
                            <a href="post_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <a href="post_create.php">
        <button>게시물 작성</button>
    </a>
</body>
</html>

<?php
$conn->close();
?>
