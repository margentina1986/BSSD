<?php
require __DIR__ . '/../../config/db_connect.php';

/* 対象テーブルとID取得 */
$table = $_GET['table'] ?? '';
$id    = $_GET['id'] ?? '';

$validTables = ['m_songs','m_members','m_parts','m_instrument','m_performances'];
if (!in_array($table, $validTables) || !$id) {
    die('不正なアクセスです');
}

/* PK 名マップ */
$pkMap = [
    'm_songs'        => 'song_id',
    'm_members'      => 'member_id',
    'm_parts'        => 'part_id',
    'm_instrument'   => 'instrument_id',
    'm_performances' => 'performance_id'
];
$pk = $pkMap[$table];

/* 削除処理 */
try {
    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE {$pk} = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('削除対象のデータが存在しません');
    }

    $success = true;
    $message = '削除が完了しました';
} catch (PDOException $e) {
    // 外部キー制約などで削除できない場合
    $success = false;
    $message = '削除に失敗しました: ' . $e->getMessage();
}
?>

<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>削除結果</title>
<link rel="stylesheet" href="styles/Admin.css">
<style>
p.success { color: green; }
p.error   { color: red; }
a.button { padding: 4px 8px; background: #007bff; color: white; text-decoration: none; border-radius: 3px; }
a.button:hover { background: #0056b3; }
</style>
</head>
<body>

<h1>削除結果（<?= htmlspecialchars($table) ?>）</h1>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($message) ?></p>
<?php else: ?>
    <p class="error"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<p>
    <a class="button" href="list.php?table=<?= $table ?>">一覧に戻る</a>
    <a class="button" href="Admin.php">管理画面トップへ戻る</a>
</p>

</body>
</html>
