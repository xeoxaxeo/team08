<?php
/*
 * game_roster.php: 복합 그룹화(GROUP BY WITH ROLLUP)를 사용하여 특정 경기의 출전 명단과 포지션별 인원수를 집계
 */

// 1. DB 연결
require_once 'db_connect.php';

// 2. 사용자 입력 받기
// '2015ALS' (2015년 아메리칸 리그 올스타)를 테스트용 기본값으로 설정
$gameID = $_REQUEST['gameID'] ?? '2015ALS';
$error_message = '';
$roster = [];
$output = []; 

// 3. SQL 쿼리 준비 (GROUP BY WITH ROLLUP)
if (!empty($gameID)) {
    try {
        $sql = "
            SELECT
                IF(GROUPING(a.startingPos) = 1, '총계 (Total)', a.startingPos) AS position,
                IF(GROUPING(m.nameFirst) = 1, '', m.nameFirst) AS firstName,
                IF(GROUPING(m.nameLast) = 1, '', m.nameLast) AS lastName,
                COUNT(a.playerID) AS playerCount
            FROM
                AllstarFull a
            JOIN
                Master m ON a.playerID = m.playerID
            WHERE
                a.gameID = :gameID
            GROUP BY
                a.startingPos, m.nameFirst, m.nameLast WITH ROLLUP
            HAVING
                -- (1,1,1) 개별 선수 행은 playerCount가 1이므로 제외, 소계/총계 행만 남기기
                -- (0,0,0) 총계 행 OR (1,0,0) 포지션별 소계 행
                GROUPING(a.startingPos) = 1 OR GROUPING(m.nameFirst) = 1
            ORDER BY
                -- 총계(Total) 행을 가장 마지막에 표시
                GROUPING(a.startingPos) ASC,
                -- 포지션별 소계 행을 인원수(playerCount) 내림차순으로 정렬
                playerCount DESC,
                -- 포지션 이름 오름차순
                position ASC;
        ";

        // 5. Prepared Statement 실행
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['gameID' => $gameID]);
        $roster = $stmt->fetchAll();

        if (empty($roster)) {
            $error_message = "'${gameID}'에 대한 데이터가 없습니다.";
        }

    } catch (\PDOException $e) {
        $error_message = "쿼리 실행 오류: " . $e->getMessage();
    }
} else {
    $error_message = "Game ID가 입력되지 않았습니다.";
}

// 6. 백엔드 결과 출력 (JSON)
header('Content-Type: application/json');

if ($error_message) {
    $output = ['error' => $error_message];
} else {
    $output = [
        'gameID' => $gameID,
        'rosterDistribution' => $roster
    ];
}

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); // 유니코드 깨짐 방지


/*
 * -----------------------------------------------------------------------------
 * [프론트엔드 코드 주석 처리]
 * 아래는 PHP 로직을 브라우저에서 바로 테스트하기 위한 HTML/CSS 코드로 참고하시면 되겠습니다.
 * -----------------------------------------------------------------------------
 */
/*
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>경기별 명단 및 포지션 분포 (ROLLUP)</title>
    <style>
        body { font-family: 'Malgun Gothic', sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        form { margin-bottom: 20px; }
        label { font-weight: bold; }
        input[type="text"] { padding: 5px; border: 1px solid #ccc; border-radius: 4px; }
        input[type="submit"] { padding: 5px 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; max-width: 600px; border-collapse: collapse; margin-top: 20px; background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #e9ecef; }
        .error { color: red; font-weight: bold; }
        tr[data-type="subtotal"] { background-color: #f8f9fa; font-weight: bold; }
        tr[data-type="total"] { background-color: #333; color: white; font-weight: bold; font-size: 1.1em; }
    </style>
</head>
<body>
    <h1>경기별 출전 명단 및 포지션 분포 (GROUP BY WITH ROLLUP)</h1>
    <p>특정 경기의 포지션별 인원수와 총 인원을 집계합니다. (복합 그룹화)</p>

    <form action="game_roster.php" method="GET">
        <label for="gameID">게임 ID:</label>
        <input type="text" id="gameID" name="gameID" value="<?= htmlspecialchars($gameID) ?>" placeholder="예: 2015ALS">
        <input type="submit" value="조회">
    </form>

    <?php if ($error_message): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php elseif (empty($gameID) && !$roster): ?>
        <p>게임 ID를 입력하고 조회 버튼을 눌러주세요.</p>
    <?php elseif (empty($roster)): ?>
        <p>"<?= htmlspecialchars($gameID) ?>"에 대한 데이터가 없습니다.</p>
    <?php else: ?>
        <h2>"<?= htmlspecialchars($gameID) ?>" 경기 포지션 분포</h2>
        <table>
            <thead>
                <tr>
                    <th>포지션 (Position)</th>
                    <th>인원수 (Player Count)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roster as $row): ?>
                    <?php
                        // ROLLUP 결과에 따라 스타일 구분을 위한 data-type 설정
                        $dataType = 'subtotal'; // 기본값 (포지션별 소계)
                        if ($row['position'] === '총계 (Total)') {
                            $dataType = 'total';
                        }
                    ?>
                    <tr data-type="<?= $dataType ?>">
                        <td><?= htmlspecialchars($row['position']) ?></td>
                        <td><?= htmlspecialchars($row['playerCount']) ?> 명</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>
*/
?>