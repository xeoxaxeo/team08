<?php
/*
 * player_trend.php: Window 함수를 사용하여 특정 선수의 연도별 성적 추이를 보여줌
 */

// 1. DB 연결
require_once 'db_connect.php';

// 2. 사용자 입력 받기 (GET 또는 POST): 'test'를 테스트용 기본값으로 설정
$playerID = $_REQUEST['playerID'] ?? 'test';
$error_message = '';
$stats = [];
$output = []; // JSON으로 출력할 최종 배열

// 3. SQL 쿼리 준비 (Window 함수 사용): playerID가 입력된 경우에만 쿼리 실행
if (!empty($playerID)) {
    try {
        $sql = "
            WITH PlayerStats AS (
                SELECT
                    yearID,
                    teamID,
                    G,  -- 출전 경기 수
                    AB, -- 타수
                    H,  -- 안타
                    HR, -- 홈런
                    RBI,
                    SO, -- 삼진
                    (H / NULLIF(AB, 0)) AS battingAvg,
                    LAG(HR, 1, 0) OVER (PARTITION BY playerID ORDER BY yearID) AS prev_HR,
                    LAG(RBI, 1, 0) OVER (PARTITION BY playerID ORDER BY yearID) AS prev_RBI,
                    LAG((H / NULLIF(AB, 0)), 1, 0.0) OVER (PARTITION BY playerID ORDER BY yearID) AS prev_battingAvg
                FROM
                    Batting
                WHERE
                    playerID = :playerID
            )
            -- 4. 최종 결과 선택
            -- CTE에서 계산된 값과, '작년 대비 변화량' (예: HR - prev_HR)을 함께 조회합니다.
            SELECT
                p.yearID,
                t.name AS teamName, -- Teams 테이블과 JOIN 하여 팀 이름 가져오기
                p.G,
                p.AB,
                p.H,
                p.HR,
                (p.HR - p.prev_HR) AS hr_change,
                p.RBI,
                (p.RBI - p.prev_RBI) AS rbi_change,
                p.battingAvg,
                (p.battingAvg - p.prev_battingAvg) AS avg_change,
                p.SO
            FROM
                PlayerStats p
            JOIN
                Teams t ON p.teamID = t.teamID AND p.yearID = t.yearID
            WHERE
                p.AB > 50  -- 유의미한 데이터 확보를 위해 타석 50 이상인 시즌만 필터링
            ORDER BY
                p.yearID DESC; -- 최근 연도부터 보여주기
        ";

        // 5. Prepared Statement 실행
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['playerID' => $playerID]);
        $stats = $stmt->fetchAll();

        if (empty($stats)) {
             $error_message = "'${playerID}'에 대한 데이터가 없거나, AB 50 이상인 시즌이 없습니다.";
        }

    } catch (\PDOException $e) {
        $error_message = "쿼리 실행 오류: " . $e->getMessage();
    }
} else {
    $error_message = "선수 ID가 입력되지 않았습니다.";
}

// 6. 백엔드 결과 출력 (JSON): 프론트엔드와 연동하기 위해 결과를 JSON으로 반환
header('Content-Type: application/json');

if ($error_message) {
    $output = ['error' => $error_message];
} else {
    $output = [
        'playerID' => $playerID,
        'stats' => $stats
    ];
}

echo json_encode($output, JSON_PRETTY_PRINT); // JSON을 예쁘게 출력

/*
 * -----------------------------------------------------------------------------
 * [프론트엔드 코드 주석 처리] :아래는 PHP 로직을 브라우저에서 바로 테스트하기 위한 HTML/CSS 코드이므로 지워도 무관합니다!
 * -----------------------------------------------------------------------------
 */
/*
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>선수 성적 추이 (Window 함수)</title>
    <style>
        body { font-family: 'Malgun Gothic', sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        form { margin-bottom: 20px; }
        label { font-weight: bold; }
        input[type="text"] { padding: 5px; border: 1px solid #ccc; border-radius: 4px; }
        input[type="submit"] { padding: 5px 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #e9ecef; }
        .error { color: red; font-weight: bold; }
        .change-up { color: green; }
        .change-down { color: red; }
        .change-zero { color: gray; }
    </style>
</head>
<body>
    <h1>선수 연도별 성적 추이 (Window 함수)</h1>
    <p>특정 선수의 연도별 성적과 작년 대비 변화량을 보여줍니다. (Window Function: LAG)</p>

    <form action="player_trend.php" method="GET">
        <label for="playerID">선수 ID:</label>
        <input type="text" id="playerID" name="playerID" value="<?= htmlspecialchars($playerID) ?>" placeholder="예: test">
        <input type="submit" value="조회">
    </form>

    <?php if ($error_message): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php elseif (empty($playerID) && !$stats): ?>
        <p>선수 ID를 입력하고 조회 버튼을 눌러주세요.</p>
    <?php elseif (empty($stats)): ?>
        <p>"<?= htmlspecialchars($playerID) ?>"에 대한 데이터가 없거나, AB 50 이상인 시즌이 없습니다.</p>
    <?php else: ?>
        <h2>"<?= htmlspecialchars($playerID) ?>" 선수 성적 추이</h2>
        <table>
            <thead>
                <tr>
                    <th>연도</th>
                    <th>팀</th>
                    <th>경기(G)</th>
                    <th>타수(AB)</th>
                    <th>안타(H)</th>
                    <th>타율(AVG)</th>
                    <th>타율 변화</th>
                    <th>홈런(HR)</th>
                    <th>홈런 변화</th>
                    <th>타점(RBI)</th>
                    <th>타점 변화</th>
                    <th>삼진(SO)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['yearID']) ?></td>
                        <td><?= htmlspecialchars($row['teamName']) ?></td>
                        <td><?= htmlspecialchars($row['G']) ?></td>
                        <td><?= htmlspecialchars($row['AB']) ?></td>
                        <td><?= htmlspecialchars($row['H']) ?></td>
                        <td><?= number_format($row['battingAvg'], 3) ?></td>
                        <td><?= formatChange($row['avg_change'], 3) ?></td>
                        <td><?= htmlspecialchars($row['HR']) ?></td>
                        <td><?= formatChange($row['hr_change']) ?></td>
                        <td><?= htmlspecialchars($row['RBI']) ?></td>
                        <td><?= formatChange($row['rbi_change']) ?></td>
                        <td><?= htmlspecialchars($row['SO']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>

<?php
// 변화량을 포맷팅하는 헬퍼 함수
function formatChange($change, $precision = 0) {
    if ($change > 0) {
        $class = 'change-up';
        $prefix = '▲+';
    } elseif ($change < 0) {
        $class = 'change-down';
        $prefix = '▼'; // 음수 기호는 $change에 포함됨
    } else {
        $class = 'change-zero';
        $prefix = '-';
    }
    
    $value = number_format($change, $precision);
    if ($change == 0) $value = '0'; // 0.000 대신 0
    
    return "<span class=\"$class\">$prefix" . ($prefix === '▼' ? $value : ltrim($value, '+')) . "</span>";
}
*/
?>
