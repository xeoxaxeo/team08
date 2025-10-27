<!DOCTYPE html>
<html>
<head>
    <title>회원가입</title>
</head>
<body>
    <h1>회원가입</h1>
    <form action="register_process.php" method="POST">
        <label for="userId">아이디:</label>
        <input type="text" id="userId" name="userId" required>
        <br>
        <label for="userPW">비밀번호:</label>
        <input type="password" id="userPW" name="userPW" required>
        <br>
        <label for="userName">이름:</label>
        <input type="text" id="userName" name="userName" required>
        <br>
        <input type="submit" value="가입">
    </form>
</body>
</html>