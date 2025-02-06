<?php
    // 세션 시작
    session_start();
    session_destroy(); // 모든 세션 삭제
    // 로그인 페이지로 이동
    header("Refresh = 2; url=login3.html");
    echo"로그아웃되었습니다! 2초 뒤 로그인 페이지로 이동합니다.";
    exit();
?>