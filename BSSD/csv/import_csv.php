<!--一旦全件削除・全件書き込み方式で作ったが外部キー制約で引っかかるので
　　アップデート&インサート方式で作り直しする。-->

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
error_log('TABLE = ' . ($_POST['table'] ?? 'NULL'));

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

$csvPath = __DIR__ . '/../csv/' . basename($_FILES['csv_file']['name']);
move_uploaded_file($_FILES['csv_file']['tmp_name'], $csvPath);

// CSV 読み込み
if (($handle = fopen($csvPath, 'r')) === false) {
    echo json_encode(['success' => false, 'error' => 'CSVファイルを開けません']);
    exit;
}

// 1行目をヘッダーとして読み飛ばす
$headers = fgetcsv($handle);

// DB トランザクション開始
$pdo->beginTransaction();

try {
    $tables = [
    'm_songs' => ['song_id','song_name','work_title'],
    'm_members' => ['member_id','member_name','member_hiragana','member_katakana','member_alphabet','member_other','search_count','search_count_fixed','is_display'],
    'm_parts' => ['part_id','part_name'],
    'm_instrument' => ['instrument_id','instrument_name','part_id'],
    'm_performances' => ['performances_id','song_id','member_id','instrument_id'],
    ];

    $tableName = $_POST['table'] ?? '';
    if (!isset($tables[$tableName])) {
        echo json_encode(['success' => false, 'error' => '不正なテーブル名です']);
        exit;
    }

    $columns = $tables[$tableName];

    // 全件削除
    $pdo->exec("DELETE FROM `$tableName`");

    // INSERT 文作成
    $placeholders = implode(',', array_fill(0, count($columns), '?'));
    $sql = "INSERT INTO $tableName (" . implode(',', $columns) . ") VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);

    // CSV 読み込み
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) !== count($columns)) {
            throw new Exception("CSVの列数がテーブルと一致しません");
        }
        $stmt->execute($row);
    }


    $pdo->commit();
    fclose($handle);

    // CSV は不要なので削除
    unlink($csvPath);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    fclose($handle);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
