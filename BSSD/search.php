<?php
require 'db_connect.php';

$results = [];

/* ========= 検索処理 ========= */
if (!empty($_GET)) {

    $part       = $_GET['part'] ?? '';
    $instrument = $_GET['instrument'] ?? '';
    $members    = array_filter($_GET['member'] ?? []);

    $sql = "
    SELECT DISTINCT
        s.song_id,
        s.song_name,
        s.work_title
    FROM m_songs s
    JOIN m_performances p ON s.song_id = p.song_id
    JOIN m_instrument i   ON p.instrument_id = i.instrument_id
    JOIN m_parts pa       ON i.part_id = pa.part_id
    WHERE 1=1
    ";

    $params = [];

    if ($part !== '') {
        $sql .= " AND pa.part_id = ?";
        $params[] = $part;
    }

    if ($instrument !== '') {
        $sql .= " AND i.instrument_id = ?";
        $params[] = $instrument;
    }

    // 複数メンバー AND 検索
    foreach ($members as $member_id) {
        $sql .= "
        AND EXISTS (
            SELECT 1
            FROM m_performances p2
            WHERE p2.song_id = s.song_id
              AND p2.member_id = ?
        )";
        $params[] = $member_id;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ===== メンバー検索回数を加算 =====
if (!empty($members)) {
    $placeholders = implode(',', array_fill(0, count($members), '?'));
    $updateSql = "
        UPDATE m_members
        SET search_count = search_count + 1
        WHERE member_id IN ($placeholders)
    ";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute($members);
}

}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Search</title>
</head>

<body id="page-top">
<nav id="menu"></nav>

<h1>Search</h1>
<h2>パート → 楽器 → メンバーを選択して検索</h2>

<form method="GET" action="search.php">

  <!-- パート -->
  <select id="part" name="part">
    <option value="">-- パート選択 --</option>
    <?php
      $stmt = $pdo->query("SELECT part_id, part_name FROM m_parts ORDER BY part_id");
      foreach ($stmt as $row) {
        echo '<option value="'.$row['part_id'].'">'
           . htmlspecialchars($row['part_name'])
           . '</option>';
      }
    ?>
  </select>

  <!-- 楽器 -->
  <select id="instrument" name="instrument">
    <option value="">-- 楽器選択 --</option>
  </select>

  <!-- メンバー（複数選択可） -->
  <select id="member" name="member[]" multiple size="5">
    <option value="">-- メンバー選択 --</option>
  </select>

  <br><br>
  <button type="submit">検索</button>

</form>

<?php if (!empty($_GET)): ?>
<hr>
<h2>検索結果</h2>

<?php if (empty($results)): ?>
  <p>該当する楽曲はありません</p>
<?php else: ?>
  <?php foreach ($results as $row): ?>
    <div class="song">
      <h3><?= htmlspecialchars($row['song_name']) ?></h3>
      <p>収録作品：<?= htmlspecialchars($row['work_title']) ?></p>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>

<script>
/* ===== パート → 楽器 ===== */
document.getElementById('part').addEventListener('change', function () {
    const partId = this.value;
    const instrument = document.getElementById('instrument');
    const member = document.getElementById('member');

    instrument.innerHTML = '<option value="">-- 楽器選択 --</option>';
    member.innerHTML = '<option value="">-- メンバー選択 --</option>';

    if (!partId) return;

    fetch('ajax_instrument.php?part_id=' + partId)
        .then(res => res.text())
        .then(html => instrument.innerHTML += html);
});

/* ===== 楽器 → メンバー ===== */
document.getElementById('instrument').addEventListener('change', function () {
    const instrumentId = this.value;
    const member = document.getElementById('member');

    member.innerHTML = '<option value="">-- メンバー選択 --</option>';

    if (!instrumentId) return;

    fetch('ajax_member.php?instrument_id=' + instrumentId)
        .then(res => res.text())
        .then(html => member.innerHTML += html);
});
</script>

</body>
</html>
