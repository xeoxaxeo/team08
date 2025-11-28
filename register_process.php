<?php
// 2170045 서자영
include 'db_connect.php';

// 폼 데이터 받기
$userId = $_POST['userId'];
$userPW = $_POST['userPW'];
$userName = $_POST['userName'];

// 비밀번호 암호화
$hashedPW = password_hash($userPW, PASSWORD_DEFAULT);

// 아이디 중복 체크
$sql_check = "SELECT * FROM Users WHERE userId = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $userId);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "<script>
            alert('The Id is already exists.');
            window.history.back();
          </script>";
} else {
    // DB에 등록
    $sql_insert = "INSERT INTO Users (userId, userPW, userName) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sss", $userId, $hashedPW, $userName);

    if ($stmt_insert->execute()) {
        echo "<script>
                alert('Successed to sign up.');
                location.href = 'login.php';
              </script>";
    } else {
        echo "<script>
                alert('Failed to sign up: " . $conn->error . "');
                window.history.back();
              </script>";
    }
    $stmt_insert->close();
}
$stmt_check->close();
$conn->close();
?>
