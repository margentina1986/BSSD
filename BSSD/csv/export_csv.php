<?php
require __DIR__ . '/../config/db_connect.php';

$table = $_GET['table'] ?? '';
$tables = ['m_songs','m_members','m_parts','m_instrument','m_performances'];
if (!in_array($table, $tables)) {
    http_response_code(400);
    echo "不正なテーブルです";
    exit;
}

$stmt = $pdo->query("SELECT * FROM $table");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $table . '.csv"');

$output = fopen('php://output', 'w');

// ヘッダー行
if ($rows) {
    fputcsv($output, array_keys($rows[0]));
}

// データ行
foreach ($rows as $row) {
    fputcsv($output, $row);
}
fclose($output);
exit;
