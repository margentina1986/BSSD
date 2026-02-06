<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'POSTのみ対応']);
    exit;
}

if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'CSVファイルが正しくアップロードされていません']);
    exit;
}

/*  テーブル定義  */
$tables = [
    'm_songs' => [
        'pk' => 'song_id',
        'columns' => ['song_id','song_name','work_title']
    ],
    'm_members' => [
        'pk' => 'member_id',
        'columns' => ['member_id','member_name','member_hiragana','member_katakana','member_alphabet','member_other','search_count','search_count_fixed','is_display']
    ],
    'm_parts' => [
        'pk' => 'part_id',
        'columns' => ['part_id','part_name']
    ],
    'm_instrument' => [
        'pk' => 'instrument_id',
        'columns' => ['instrument_id','instrument_name','part_id']
    ],
    'm_performances' => [
        'pk' => 'performances_id',
        'columns' => ['performances_id','song_id','member_id','instrument_id']
    ],
];

$tableName = $_POST['table'] ?? '';
if (!isset($tables[$tableName])) {
    echo json_encode(['success' => false, 'error' => '不正なテーブル名です']);
    exit;
}

$columns = $tables[$tableName]['columns'];
$pk = $tables[$tableName]['pk'];

/*  CSV 読み込み  */
$handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
if (!$handle) {
    echo json_encode(['success' => false, 'error' => 'CSVを開けません']);
    exit;
}

// ヘッダ読み飛ばし
$headers = fgetcsv($handle, 0, ',');

// BOM除去
if ($headers && isset($headers[0])) {
    $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
}

// トランザクション開始
$pdo->beginTransaction();

try {
    // INSERT 部分
    $placeholders = implode(',', array_fill(0, count($columns), '?'));

    // UPDATE 部分（PK以外）
    $updateCols = array_filter($columns, fn($c) => $c !== $pk);
    $updateSql = implode(
        ', ',
        array_map(fn($c) => "$c = VALUES($c)", $updateCols)
    );

    $sql = "
        INSERT INTO `$tableName` (" . implode(',', $columns) . ")
        VALUES ($placeholders)
        ON DUPLICATE KEY UPDATE $updateSql
    ";

    $stmt = $pdo->prepare($sql);

    $count = 0;
    while (($row = fgetcsv($handle, 0, ',')) !== false) {

        // ヘッダ行だったらスキップ
        if ($row[0] === $pk) {
            continue;
        }
        if (count($row) !== count($columns)) {
            throw new Exception('CSVの列数がテーブル定義と一致しません');
        }
        $stmt->execute($row);
        $count++;
    }


    $pdo->commit();
    fclose($handle);

    echo json_encode([
        'success' => true,
        'count' => $count
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    fclose($handle);

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
