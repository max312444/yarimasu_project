<?php
// 세션 시작
session_start();

// 모든 세션 변수 제거
$_SESSION = array();

// 세션 쿠키도 제거 (완전한 로그아웃을 위해)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 세션 파기
session_destroy();

// 2초 후 로그인 페이지로 이동
header("Location: login3.html");
exit();
?>
