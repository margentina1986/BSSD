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

<body id="page-top">
    <nav id="menu"></nav>

    <h1>Version</h1>
    <h2>数ある楽曲のバージョン違いについて、当HPでの取り扱い方をまとめています。</h2>
    <h3>曲名をクリックして詳細を確認できます。</h3>

    <div class="table-wrapper">
        <table class="song-table">
            <thead>
                <tr>
                    <th>曲名</th>
                    <th>バージョン</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($grouped as $title => $songs): ?>
                <?php $rowspan = count($songs); ?>
                <?php foreach ($songs as $i => $song): ?>
                    <tr>
                        <?php if ($i === 0): ?>
                            <td rowspan="<?= $rowspan ?>">
                                <a href="song_detail.php?title=<?= rawurlencode($title) ?>" class="song-link">
                                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </td>
                        <?php endif; ?>
                        <td><?= nl2br(htmlspecialchars($song['version'], ENT_QUOTES, 'UTF-8')) ?></td>
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