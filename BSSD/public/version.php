<?php
$rows = require __DIR__ . '/../data/version_table.php';

// 曲名ごとにグルーピング
$grouped = [];
foreach ($rows as $row) {
    $grouped[$row['title']][] = $row;
}

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Version</title>
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="./styles/version.css">
    <link rel="stylesheet" href="./styles/footer.css">
</head>

<body>
    <nav id="menu"></nav>

    <h1>Version</h1>
    <h2>数ある楽曲のバージョン違いについて、当HPでの取り扱い方を表形式でまとめています。</h2>

    <div class="table-wrapper">
        <table class="song-table">
            <thead>
                <tr>
                    <th>曲名</th>
                    <th>バージョン</th>
                    <th>初回収録作品</th>
                    <!-- <th>違い</th> -->
                    <th>当HPでの取り扱い</th>
                    <th>備考</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($grouped as $title => $songs): ?>
                    <?php $rowspan = count($songs); ?>
                    <?php foreach ($songs as $i => $song): ?>
                        <tr>
                            <?php if ($i === 0): ?>
                                <td rowspan="<?= $rowspan ?>">
                                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            <?php endif; ?>

                            <td><?= nl2br(htmlspecialchars($song['version'], ENT_QUOTES, 'UTF-8')) ?></td>
                            <td><?= nl2br(htmlspecialchars($song['first_work'], ENT_QUOTES, 'UTF-8')) ?></td>
                            <!-- <td><?= nl2br(htmlspecialchars($song['difference'], ENT_QUOTES, 'UTF-8')) ?></td>-->
                            <td><?= nl2br(htmlspecialchars($song['policy'], ENT_QUOTES, 'UTF-8')) ?></td>
                            <td><?= nl2br(htmlspecialchars($song['note'], ENT_QUOTES, 'UTF-8')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
    <script src="scripts/menu.js"></script>
    <?php include 'parts/footer.php'; ?>
</body>
</html>
