<?php
$rows = require __DIR__ . '/../data/qa_list.php';

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q & A</title>
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="./styles/qa.css">
    <link rel="stylesheet" href="./styles/footer.css">
</head>

<body id="page-top">
    <nav id="menu"></nav>

    <h1>Q & A</h1>
    <h2>想定質問とその回答を記載しています。よくあるご質問については稀に追加されることがあります。</h2>

    <table class="qa_list">
        <thead>
            <tr>
                <th>No.</th>
                <th>質問</th>
                <th>回答</th>
            </tr>
        </thead>
        <tbody>
           <?php foreach ($rows as $item): ?>
                <tr>
                    <td><?= nl2br(htmlspecialchars($item['number'], ENT_QUOTES, 'UTF-8')) ?></td>
                    <td><?= nl2br(htmlspecialchars($item['q'], ENT_QUOTES, 'UTF-8')) ?></td>
                    <td><?= nl2br(htmlspecialchars($item['a'], ENT_QUOTES, 'UTF-8')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="scripts/menu.js"></script>
    <?php include 'parts/footer.php'; ?>
</body>
</html>
