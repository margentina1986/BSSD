<?php
require __DIR__ . '/../config/db_connect.php'; // DBへ接続

$maxRows = 20;

// 収録参加曲数ランキング（メンバー&楽器の組み合わせ単位）
$sqlSongCount = "
SELECT
  m.member_id,
  m.member_name,
  p.part_name AS instrument_part_name,
  COUNT(DISTINCT perf.song_id) AS song_count
FROM m_members m
JOIN m_performances perf ON m.member_id = perf.member_id
JOIN m_instrument i ON perf.instrument_id = i.instrument_id
JOIN m_parts p ON i.part_id = p.part_id
WHERE m.is_display = 1
GROUP BY m.member_id, perf.instrument_id, m.member_name, p.part_name
ORDER BY song_count DESC
LIMIT :maxRows
";

$stmt = $pdo->prepare($sqlSongCount);
$stmt->bindValue(':maxRows', $maxRows, PDO::PARAM_INT);
$stmt->execute();

$songCountRanking = [];
$rank = 1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $songCountRanking[] = [
        'rank' => $rank++,
        'name' => $row['member_name'],
        'instrument' => $row['instrument_part_name'],
        'count' => $row['song_count'],
    ];
}

// 累計検索回数ランキング（毎日0時に別ファイルのPythonでバッチ処理）
$sqlSearchCount = "
SELECT
    member_name,
    search_count_fixed
FROM m_members
WHERE is_display = 1
ORDER BY search_count_fixed DESC
LIMIT :maxRows
";

$stmt = $pdo->prepare($sqlSearchCount);
$stmt->bindValue(':maxRows', $maxRows, PDO::PARAM_INT);
$stmt->execute();

$searchCountRanking = [];
$rank = 1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $searchCountRanking[] = [
        'rank' => $rank++,
        'name' => $row['member_name'],
        'count' => $row['search_count_fixed']
    ];
}
?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="./styles/ranking.css">
    <link rel="stylesheet" href="./styles/footer.css">
</head>

<body id="page-top">
<nav id="menu"></nav>

<h1>Ranking</h1>
<h2>収録参加曲数のランキングと、当HPで検索された回数（日次更新）のランキングが表示されます</h2>

<div class="ranking-wrap">

    <!-- 収録参加曲数ランキング -->
    <section class="ranking-box">
        <h3>収録参加曲数ランキング</h3>
        <table>
            <thead>
                <tr>
                    <th>順位</th>
                    <th>名前</th>
                    <th>担当楽器</th>
                    <th>曲数</th>
                </tr>
            </thead>
            <tbody>
            <?php for ($i = 0; $i < $maxRows; $i++): ?>
                <?php $row = $songCountRanking[$i] ?? null; ?>
                <?php
                    $rankClass = '';
                    if (isset($row['rank'])) {
                        if ($row['rank'] == 1) $rankClass = 'rank-gold';
                        elseif ($row['rank'] == 2) $rankClass = 'rank-silver';
                        elseif ($row['rank'] == 3) $rankClass = 'rank-bronze';
                    }
                ?>
                <tr class="<?= $rankClass ?>">
                    <td><?= isset($row['rank']) ? $row['rank'] . '位' : ($i + 1) . '位' ?></td>
                    <td><?= $row['name'] ?? '' ?></td>
                    <td><?= $row['instrument'] ?? '' ?></td>
                    <td><?= $row['count'] ?? '' ?></td>
                </tr>
            <?php endfor; ?>

            </tbody>
        </table>
        <p class="content-text">・明石さんと徳永さんはもっと弾いてるイメージだったけど<br>　打ち込みが多いので演奏した曲数は伸びず。<br>・明石さんのコーラスはクレジットされてるだけで<br>　実際には歌ってない曲があるので水増しされてますね。<br>・2021年から参加の種子田さんが早くもランクイン。</p>
    </section>

    <!-- 累計検索回数ランキング -->
    <section class="ranking-box">
        <h3>累計検索回数ランキング（毎日0時更新）</h3>
        <table>
            <thead>
                <tr>
                    <th>順位</th>
                    <th>名前</th>
                    <th>検索回数</th>
                </tr>
            </thead>
            <tbody>
           <?php for ($i = 0; $i < $maxRows; $i++): ?>
                <?php $row = $searchCountRanking[$i] ?? null; ?>
                <?php
                    $rankClass = '';
                    if (isset($row['rank'])) {
                        if ($row['rank'] == 1) $rankClass = 'rank-gold';
                        elseif ($row['rank'] == 2) $rankClass = 'rank-silver';
                        elseif ($row['rank'] == 3) $rankClass = 'rank-bronze';
                    }
                ?>
                <tr class="<?= $rankClass ?>">
                    <td><?= isset($row['rank']) ? $row['rank'] . '位' : ($i + 1) . '位' ?></td>
                    <td><?= $row['name'] ?? '' ?></td>
                    <td><?= $row['count'] ?? '' ?></td>
                </tr>
            <?php endfor; ?>

            </tbody>
        </table>
        
    </section>

</div>
    <script src="scripts/menu.js"></script>
    <?php include 'parts/footer.php'; ?>
</body>
</html>
