<?php
// 2170045 서자영
session_start();
session_destroy();

echo "<script>
        alert('Logout successfully.');
        location.href = 'index.php'; // 메인 페이지로 이동
      </script>";
?>
