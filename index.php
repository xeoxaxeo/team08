<?php
session_start();
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>야구 빅데이터 분석</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 10px;}
        .header h1 { margin: 0; }
        .login-menu { text-align: right; }
        nav { margin-top: 15px; }
        nav a { margin-right: 15px; text-decoration: none; font-size: 1.1em; }
    </style>
</head>
<body>
    
    <!-- 로그인/회원가입  -->
    <div class="header">
        <h1>team08 칼퇴기원</h1>
        
        <div class="login-menu">
            <?php if (isset($_SESSION['userid'])): ?>
                <!-- 1. 로그인 성공 -->
                <p>
                    <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>님 환영합니다!
                </p>
                <a href="mypage.php">마이페이지</a> | 
                <a href="logout_process.php">로그아웃</a>
                
            <?php else: ?>
                <!-- 2. 로그인 안 됨 -->
                <p>로그인이 필요합니다.</p>
                <a href="login.php">로그인</a> | 
                <a href="register.php">회원가입</a>
                
            <?php endif; ?>
        </div>
    </div>

    <nav>
        <a href="index.php">홈</a> |
        <a href="#">선수별 분석</a> |
        <a href="#">팀별 분석</a> |
        <a href="#">시즌/경기별 분석</a>
    </nav>
    
    <hr>
    
    <h2>프로젝트 홈</h2>
    <p>프로젝트 개요, 팀원 소개 등</p>
    
    
    <h3>선수별 분석</h3>
    <ul>
        <!-- <li><a href="#">선수 성적 추이 (Windowing)</a></li> -->
        <li><a href="salary_ranking.php">선수 연봉 순위 (Ranking)</a></li>
    </ul>

    <h3>팀별 분석</h3>
    <ul>
        <li><a href="team_ranking.php">팀 시즌별 성적 순위 (Ranking)</a></li>
        <!-- <li><a href="#">팀별 총/평균 연봉 비교 (OLAP)</a></li> -->
        <!-- <li><a href="#">포지션별 선수 성적 비교 (Aggregate)</a></li> -->
    </ul>

    <h3>시즌/경기별 분석</h3>
    <ul>
        <!-- <li><a href="#">리그별 연봉 비교 (OLAP)</a></li> -->
        <!-- <li><a href="#">경기별 포지션 분포 (Aggregate)</a></li> -->
    </ul>

    <?php
        $conn->close();
    ?>
</body>
</html>
