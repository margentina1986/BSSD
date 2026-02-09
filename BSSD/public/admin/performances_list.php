<?php
require __DIR__ . '/../../config/db_connect.php';

// m_performances専用チェック
if (!isset($_GET['table']) || $_GET['table'] !== 'm_performances') {
    exit('この一覧はm_performances専用です');
}

// ソート列指定（デフォルトは performance_id）
$validSorts = ['performance_id', 'song_id', 'member_id', 'instrument_id'];
$sort = $_GET['sort'] ?? 'performance_id';
if (!in_array($sort, $validSorts)) $sort = 'performance_id';

// SQLでIDも取得してソート
$sql = "SELECT 
            p.performance_id, 
            p.song_id, s.song_name, 
            p.member_id, m.member_name, 
            p.instrument_id, i.instrument_name
        FROM m_performances p
        JOIN m_songs s ON p.song_id = s.song_id
        JOIN m_members m ON p.member_id = m.member_id
        JOIN m_instrument i ON p.instrument_id = i.instrument_id
        ORDER BY p.{$sort} ASC";

$stmt = $pdo->query($sql);
$performances = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>演奏曲一覧</title>
<link rel="stylesheet" href="styles/Admin.css">
<style>
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
th { background: #f0f0f0; }
a.button { padding: 4px 8px; background: #007bff; color: white; text-decoration: none; border-radius: 3px; margin-right: 4px; }
a.button:hover { background: #0056b3; }
.sort-link { font-size: 0.9em; margin-left: 4px; color: #333; text-decoration: none; }
.sort-link:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1>演奏曲一覧</h1>

<p>
    <a class="button" href="add.php?table=m_performances">追加</a>
    <a class="button" href="Admin.php">管理画面に戻る</a>
</p>

<table>
    <thead>
        <tr>
            <th>performance_id
                <a class="sort-link" href="?table=m_performances&sort=performance_id">↑ID順</a>
            </th>
            <th>曲名
                <a class="sort-link" href="?table=m_performances&sort=song_id">ID順</a>
            </th>
            <th>メンバー名
                <a class="sort-link" href="?table=m_performances&sort=member_id">ID順</a>
            </th>
            <th>楽器名
                <a class="sort-link" href="?table=m_performances&sort=instrument_id">ID順</a>
            </th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($performances)): ?>
            <tr><td colspan="5">データがありません</td></tr>
        <?php else: ?>
            <?php foreach ($performances as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['performance_id']) ?></td>
                    <td><?= htmlspecialchars($row['song_name']) ?></td>
                    <td><?= htmlspecialchars($row['member_name']) ?></td>
                    <td><?= htmlspecialchars($row['instrument_name']) ?></td>
                    <td>
                        <a class="button" href="edit.php?table=m_performances&id=<?= $row['performance_id'] ?>">編集</a>
                        <a class="button" href="delete.php?table=m_performances&id=<?= $row['performance_id'] ?>"
                           onclick="return confirm('本当に削除しますか？');">削除</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
