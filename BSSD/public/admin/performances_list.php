<?php
require __DIR__ . '/../../config/db_connect.php';

// パラメータチェック（必要なら）
if (!isset($_GET['table']) || $_GET['table'] !== 'm_performances') {
    exit('この一覧はm_performances専用です');
}

// JOIN付きのSQLで一覧取得
$sql = "SELECT p.performance_id, s.song_name, m.member_name, i.instrument_name
        FROM m_performances p
        JOIN m_songs s ON p.song_id = s.song_id
        JOIN m_members m ON p.member_id = m.member_id
        JOIN m_instrument i ON p.instrument_id = i.instrument_id";

$stmt = $pdo->query($sql);
$performances = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<title>演奏曲一覧</title>
<style>
    table, th, td { border: 1px solid #000; border-collapse: collapse; padding: 5px; }
</style>
</head>
<body>
<h1>演奏曲一覧</h1>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>曲名</th>
            <th>メンバー名</th>
            <th>楽器名</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($performances as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['performance_id']) ?></td>
            <td><?= htmlspecialchars($row['song_name']) ?></td>
            <td><?= htmlspecialchars($row['member_name']) ?></td>
            <td><?= htmlspecialchars($row['instrument_name']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
