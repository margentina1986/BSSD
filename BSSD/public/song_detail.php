<?php
$rows = require __DIR__ . '/../data/version_table.php';

$title = $_GET['title'] ?? '';

// 該当曲のデータを抽出
$songs = array_filter($rows, fn($row) => $row['title'] === $title);
$songs = array_values($songs);

if (empty($songs)) {
    header('HTTP/1.1 404 Not Found');
    $error = true;
}

// 曲名リストを重複なしで順番通りに取得
$titles = [];
foreach ($rows as $row) {
    if (!in_array($row['title'], $titles)) {
        $titles[] = $row['title'];
    }
}

// 現在の曲のインデックスを取得
$currentIndex = array_search($title, $titles);

// 前後の曲名を取得（存在しない場合はnull）
$prevTitle = ($currentIndex > 0) ? $titles[$currentIndex - 1] : null;
$nextTitle = ($currentIndex < count($titles) - 1) ? $titles[$currentIndex + 1] : null;
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="./styles/song_detail.css">
    <link rel="stylesheet" href="./styles/footer.css">
</head>

<body id="page-top">
    <nav id="menu"></nav>
    <?php if (!empty($error)): ?>
        <h1>曲が見つかりませんでした</h1>
        <p><a href="version.php">バージョン一覧に戻る</a></p>

    <?php else: ?>
        <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
            <nav class="song-nav">
                <?php if ($prevTitle): ?>
                    <a class="song-nav-prev" href="song_detail.php?title=<?= rawurlencode($prevTitle) ?>">
                        &#9664; <?= htmlspecialchars($prevTitle, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php else: ?>
                    <span class="song-nav-prev song-nav-disabled"></span>
                <?php endif; ?>

                <?php if ($nextTitle): ?>
                    <a class="song-nav-next" href="song_detail.php?title=<?= rawurlencode($nextTitle) ?>">
                        <?= htmlspecialchars($nextTitle, ENT_QUOTES, 'UTF-8') ?> &#9654;
                    </a>
                <?php else: ?>
                    <span class="song-nav-next song-nav-disabled"></span>
                <?php endif; ?>
            </nav>
        <?php foreach ($songs as $song): ?>
            <section class="version-block">
                <h2><?= nl2br(htmlspecialchars($song['version'], ENT_QUOTES, 'UTF-8')) ?></h2>

                <table class="detail-table">
                    <tr>
                        <th>初回収録作品</th>
                        <td><?= nl2br(htmlspecialchars($song['first_work'], ENT_QUOTES, 'UTF-8')) ?></td>
                    </tr>
                    <tr>
                        <th>違い</th>
                        <td><?= nl2br(htmlspecialchars($song['difference'], ENT_QUOTES, 'UTF-8')) ?></td>
                    </tr>
                    <tr>
                        <th>当HPでの取り扱い</th>
                        <td><?= nl2br(htmlspecialchars($song['policy'], ENT_QUOTES, 'UTF-8')) ?></td>
                    </tr>
                    <tr>
                        <th colspan="2">参加ミュージシャン</th>
                    </tr>
                    <tr>
                        <td colspan="2"><?= nl2br(htmlspecialchars($song['musicians'], ENT_QUOTES, 'UTF-8')) ?></td>
                    </tr>
                </table>
            </section>
        <?php endforeach; ?>

        <p><a href="version.php" class="back-link">バージョン一覧に戻る</a></p>
    <?php endif; ?>

    <script src="scripts/menu.js"></script>
    <?php include 'parts/footer.php'; ?>
</body>
</html>