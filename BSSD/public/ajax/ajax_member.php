<?php
require __DIR__ . '/../../config/db_connect.php';

$instrument_id = $_GET['instrument_id'] ?? '';

$stmt = $pdo->prepare(
    "SELECT m.member_id, m.member_name
     FROM m_members m
     JOIN m_performances p ON p.member_id = m.member_id
     WHERE p.instrument_id = ?
     GROUP BY m.member_id
     ORDER BY CAST(m.member_id AS UNSIGNED) ASC"
);
$stmt->execute([$instrument_id]);

foreach ($stmt as $row) {
    echo '<option value="' . $row['member_id'] . '">'
         . htmlspecialchars($row['member_name'], ENT_QUOTES, 'UTF-8')
         . '</option>';
}
