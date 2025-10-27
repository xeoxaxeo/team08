<?php
session_start();

if (!isset($_SESSION['userid'])) {
    echo "<script>
            alert('로그인이 필요합니다.');
            location.href = 'login.php';
          </script>";
    exit;
}
?>

// 마이페이지에서 이름 변경
<!DOCTYPE html>
<html>
<head>
    <title>마이페이지</title>
</head>
<body>
    <h1>마이페이지</h1>
    <p><strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>님 (ID: <?php echo htmlspecialchars($_SESSION['userid']); ?>)</p>
    
    <form action="mypage_process.php" method="POST">
        <label for="userName">변경할 이름:</label>
        <input type="text" id="userName" name="userName" 
               value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
        <br>
        <input type="submit" value="이름 변경">
    </form>
    <br>
    <a href="index.php">홈</a> | 
    <a href="logout_process.php">로그아웃</a> | 
    <a href="delete_account.php" style="color:red;">회원 탈퇴</a>
</body>
</html>