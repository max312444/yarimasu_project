<?php
session_start(); // 세션 시작
session_destroy(); // 모든 세션 삭제
header("Location: login2.html"); // 로그인 페이지로 이동
exit();
?>
