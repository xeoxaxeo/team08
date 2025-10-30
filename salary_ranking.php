<?php
session_start();
include 'db_connect.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : 2015; 

?>

<!DOCTYPE html>
<html>
<head>
    <title>선수 연봉 순위</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h1, h2 { text-align: center; }
        form { text-align: center; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 80%; margin: 0 auto; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        td:first-child, td:last-child { text-align: center; }
        td:nth-child(4) { text-align: right; }
    </style>
</head>
<body>

    <h1>선수 연봉 순위 (Ranking)</h1>
    
    <form action="salary_ranking.php" method="GET">
        <label for="year">연도:</label>
        <input type"number" id="year" name="year" 
               value="<?php echo htmlspecialchars($year); ?>" 
               min="1985" max="2015"> <input type="submit" value="연봉 순위 조회">
    </form>

    <h2><?php echo htmlspecialchars($year); ?>년 연봉 TOP 100</h2>
    
    <table>
        <tr>
            <th>순위</th>
            <th>선수 이름</th>
            <th>팀 이름</th>
            <th>연봉</th>
        </tr>
        <?php
        
        $sql = "SELECT 
                    m.nameFirst, 
                    m.nameLast,
                    t.name AS teamName,
                    s.salary,
                    RANK() OVER (PARTITION BY s.yearID ORDER BY s.salary DESC) as salary_rank
                FROM 
                    Salaries s
                JOIN 
                    Master m ON s.playerID = m.playerID
                JOIN 
                    Teams t ON s.teamID = t.teamID AND s.yearID = t.yearID AND s.lgID = t.lgID
                WHERE 
                    s.yearID = ?
                ORDER BY 
                    salary_rank ASC, s.salary DESC
                LIMIT 100";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['salary_rank'] . "</td>";
                echo "<td>" . htmlspecialchars($row['nameFirst'] . " " . $row['nameLast']) . "</td>";
                echo "<td>" . htmlspecialchars($row['teamName']) . "</td>";
                echo "<td>$" . number_format($row['salary']) . "</td>"; 
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>해당 연도의 데이터가 없습니다. (1985~2015년 사이로 입력하세요.)</td></tr>";
        }
        
        $stmt->close();
        $conn->close();
        ?>
    </table>
    
    <p style-align:center;"><a href="index.php">메인으로</a></p>

</body>
</html>