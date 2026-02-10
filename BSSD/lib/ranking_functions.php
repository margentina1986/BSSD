<?php
require __DIR__ . '/../config/db_connect.php'; // 接続

$maxRows = 20;

$stmt = $pdo->prepare("
    SELECT member_name, search_count_fixed
    FROM m_members
    WHERE is_display = 1
    ORDER BY search_count_fixed DESC
    LIMIT :maxRows
");
$stmt->bindValue(':maxRows', $maxRows, PDO::PARAM_INT);
$stmt->execute();

$searchCountRanking = [];
$rank = 1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $searchCountRanking[] = [
        'rank' => $rank++,
        'name' => $row['member_name'],
        'count' => $row['search_count_fixed'],
    ];
}
?>
