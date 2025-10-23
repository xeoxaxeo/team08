<?php
/*
 * db_connect.php: DB 연결 파일
 */

$host = 'localhost';  
$user = '사용자명'; 
$pass = 'PW';
$db   = 'DB명';

$conn = new mysqli($host, $user, $pass, $db);

// 연결 오류
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

// 한글 깨짐 방지
$conn->set_charset("utf8mb4");

?>