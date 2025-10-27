<!DOCTYPE html>
<html>
<head>
    <title>로그인</title>
</head>
<body>
    <h1>로그인</h1>
    <form action="login_process.php" method="POST">
        <label for="userId">아이디:</label>
        <input type="text" id="userId" name="userId" required>
        <br>
        <label for="userPW">비밀번호:</label>
        <input type="password" id="userPW" name="userPW" required>
        <br>
        <input type="submit" value="로그인">
    </form>
    <p>회원가입<a href="register.php">회원가입</a></p>
</body>
</html>