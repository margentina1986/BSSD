<?php
require __DIR__ . '/../../config/db_connect.php';

/* 対象テーブル */
$table = $_GET['table'] ?? '';
$validTables = ['m_songs','m_members','m_parts','m_instrument','m_performances'];
if (!in_array($table, $validTables)) die('不正なテーブルです');

/* PK 名マップ */
$pkMap = [
    'm_songs'        => 'song_id',
    'm_members'      => 'member_id',
    'm_parts'        => 'part_id',
    'm_instrument'   => 'instrument_id',
    'm_performances' => 'performances_id'
];
$pk = $pkMap[$table];

/* カラム名取得 */
$columns = $pdo->query("DESCRIBE {$table}")->fetchAll(PDO::FETCH_COLUMN);

/* データ取得 */
$data = $pdo->query("SELECT * FROM {$table}")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>一覧（<?= htmlspecialchars($table) ?>）</title>
<link rel="stylesheet" href="styles/Admin.css">
<style>
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
th { background: #f0f0f0; }
a.button { padding: 4px 8px; background: #007bff; color: white; text-decoration: none; border-radius: 3px; }
a.button:hover { background: #0056b3; }
</style>
</head>
<body>

<h1>一覧（<?= htmlspecialchars($table) ?>）</h1>

<p>
    <a class="button" href="add.php?table=<?= $table ?>">新規追加</a>
    <a class="button" href="Admin.php">管理画面へ戻る</a>
</p>

<table>
    <thead>
        <tr>
            <?php foreach ($columns as $col): ?>
                <th><?= htmlspecialchars($col) ?></th>
            <?php endforeach; ?>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($data)): ?>
            <tr><td colspan="<?= count($columns)+1 ?>">データがありません</td></tr>
        <?php else: ?>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <td><?= htmlspecialchars($row[$col]) ?></td>
                    <?php endforeach; ?>
                    <td>
                        <a class="button" href="edit.php?table=<?= $table ?>&id=<?= $row[$pk] ?>">編集</a>
                        <a class="button" href="delete.php?table=<?= $table ?>&id=<?= $row[$pk] ?>"
                           onclick="return confirm('本当に削除しますか？');">削除</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
