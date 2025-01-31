<?php
session_start(); // 세션 시작

// 오류 출력 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 세션 데이터 제거
session_unset(); // 모든 세션 변수 해제
session_destroy(); // 세션 파괴

// 로그아웃 메시지 출력 후 로그인 페이지로 이동
echo "<script>
    alert('You have been logged out. Returning to the login page.');
    window.location.href = 'login.html';
</script>";
exit;
?>
