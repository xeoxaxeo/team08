<?php
session_start();
include 'db_connect.php';

$year = isset($_GET['year']) ? $_GET['year'] : '2015';
$lg = isset($_GET['lg']) ? $_GET['lg'] : 'NL';

?>

<!DOCTYPE html>
<html>
<head>
    <title>팀 시즌별 성적 순위</title>
</head>
<body>
    <h1>팀 시즌별 성적 순위 (Ranking)</h1>
    
    <form action="team_ranking.php" method="GET">
        <label for="year">연도:</label>
        <input type="number" name="year" value="<?php echo htmlspecialchars($year); ?>" min="1871">
        
        <label for="lg">리그:</label>
        <select name="lg">
            <option value="NL" <?php if($lg == 'NL') echo 'selected'; ?>>National League (NL)</option>
            <option value="AL" <?php if($lg == 'AL') echo 'selected'; ?>>American League (AL)</option>
        </select>
        
        <input type="submit" value="조회">
    </form>
    <hr>

    <h2><?php echo htmlspecialchars($year) . '년 ' . htmlspecialchars($lg) . ' 리그 순위'; ?></h2>
    
    <table border="1">
        <tr>
            <th>순위 (계산)</th>
            <th>팀 이름</th>
            <th>승</th>
            <th>패</th>
            <th>순위 (공식)</th>
        </tr>
        <?php
        $sql = "SELECT 
                    name, W, L, `Rank` AS official_rank,
                    RANK() OVER (PARTITION BY yearID, lgID ORDER BY W DESC) AS calculated_rank
                FROM Teams
                WHERE yearID = ? AND lgID = ?
                ORDER BY calculated_rank ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $year, $lg);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['calculated_rank'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['W'] . "</td>";
                echo "<td>" . $row['L'] . "</td>";
                echo "<td>" . $row['official_rank'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>해당 연도/리그의 데이터가 없습니다.</td></tr>";
        }
        
        $stmt->close();
        $conn->close();
        ?>
    </table>

    <p style="text-align:center; margin-top: 20px;">
        <a href="index.php">메인으로</a>
    </p>
</body>
</html>