<?php
// 2170045 서자영
session_start();
include 'db_connect.php';

if (!isset($_SESSION['userid'])) {
    die("You need to login.");
}

$userId = $_SESSION['userid'];
$newUserName = $_POST['userName'];

// DB에 UPDATE
$sql = "UPDATE Users SET userName = ? WHERE userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $newUserName, $userId);

if ($stmt->execute()) {
    // 세션 정보도 함께 UPDATE
    $_SESSION['username'] = $newUserName;
    
    echo "<script>
            alert('Name has modified successfully.');
            location.href = 'mypage.php';
          </script>";
} else {
    echo "<script>
            alert('Failed to modify name: " . $conn->error . "');
            window.history.back();
          </script>";
}
$stmt->close();
$conn->close();
?>
