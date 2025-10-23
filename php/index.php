<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>야구 빅데이터 분석</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        nav a { margin-right: 15px; text-decoration: none; }
    </style>
</head>
<body>
    <h1>team08 칼퇴기원</h1>
    
    <nav>
        <a href="index.php">홈</a> |
        <a href="player_analysis.php">선수별 분석</a> |
        <a href="team_analysis.php">팀별 분석</a> |
        <a href="season_analysis.php">시즌/경기별 분석</a>
    </nav>
    
    <hr>
    
    <h2>프로젝트 홈</h2>
    <p>프로젝트 개요, 팀원 소개 등</p>
    
    <h3>선수별 분석 (player_analysis.php)</h3>
    <ul>
        <li><a href="player_analysis.php?func=windowing">선수 성적 추이 (Windowing)</a></li>
        <li><a href="player_analysis.php?func=ranking">선수 연봉 순위 (Ranking)</a></li>
    </ul>

    <h3>팀별 분석 (team_analysis.php)</h3>
    <ul>
        <li><a href="team_analysis.php?func=ranking">팀 시즌별 성적 순위 (Ranking)</a></li>
        <li><a href="team_analysis.php?func=olap">팀별 총/평균 연봉 비교 (OLAP)</a></li>
        <li><a href="team_analysis.php?func=aggregate">포지션별 선수 성적 비교 (Aggregate)</a></li>
    </ul>

    <h3>시즌/경기별 분석 (season_analysis.php)</h3>
    <ul>
        <li><a href="season_analysis.php?func=olap">리그별 연봉 비교 (OLAP)</a></li>
        <li><a href="season_analysis.php?func=aggregate">경기별 포지션 분포 (Aggregate)</a></li>
    </ul>

    <?php
        $conn->close();
    ?>
</body>
</html>