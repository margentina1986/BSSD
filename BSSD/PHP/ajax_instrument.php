<?php
require __DIR__ . '/../config/db_connect.php';

$part_id = $_GET['part_id'] ?? '';

$stmt = $pdo->prepare(
    "SELECT instrument_id, instrument_name
     FROM m_instrument
     WHERE part_id = ?
     ORDER BY instrument_name"
);
$stmt->execute([$part_id]);

foreach ($stmt as $row) {
    echo '<option value="'.$row['instrument_id'].'">'
       . htmlspecialchars($row['instrument_name'])
       . '</option>';
}