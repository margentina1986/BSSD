<?php
require __DIR__ . '/../config/db_connect.php';

$instrument_id = $_GET['instrument_id'] ?? '';

$stmt = $pdo->prepare(
    "SELECT DISTINCT
        m.member_id,
        m.member_name
     FROM m_members m
     JOIN m_performances p ON m.member_id = p.member_id
     WHERE p.instrument_id = ?
     ORDER BY m.member_name"
);
$stmt->execute([$instrument_id]);

foreach ($stmt as $row) {
    echo '<option value="'.$row['member_id'].'">'
       . htmlspecialchars($row['member_name'])
       . '</option>';
}