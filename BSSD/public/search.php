<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config/db_connect.php';

// ====== パート一覧取得 ======
$partStmt = $pdo->query("SELECT part_id, part_name FROM m_parts ORDER BY CAST(part_id AS UNSIGNED) ASC");
$parts = $partStmt->fetchAll(PDO::FETCH_ASSOC);

// ====== 検索処理 ======
$results = [];
$conditions = $_GET['conditions'] ?? [];

// メンバー選択があるか確認
$hasMember = false;
foreach ($conditions as $cond) {
    if (!empty($cond['member'])) {
        $hasMember = true;
        break;
    }
}

if ($hasMember) {
    $sql = "
    SELECT DISTINCT
        s.song_id,
        s.song_name,
        s.work_title
    FROM m_songs s
    WHERE 1=1
    ";
    $params = [];

    foreach ($conditions as $cond) {
        if (empty($cond['member'])) continue;

        $sql .= "
        AND EXISTS (
            SELECT 1
            FROM m_performances p
            JOIN m_instrument i ON p.instrument_id = i.instrument_id
            WHERE p.song_id = s.song_id
              AND p.member_id = ?
        ";
        $params[] = $cond['member'];

        if (!empty($cond['instrument'])) {
            $sql .= " AND i.instrument_id = ?";
            $params[] = $cond['instrument'];
        }
        if (!empty($cond['part'])) {
            $sql .= " AND i.part_id = ?";
            $params[] = $cond['part'];
        }
        $sql .= ")";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 検索回数更新
    $members = [];
    foreach ($conditions as $cond) {
        if (!empty($cond['member'])) {
            $members[] = $cond['member'];
        }
    }
    $members = array_unique($members);
    if ($members) {
        $placeholders = implode(',', array_fill(0, count($members), '?'));
        $updateSql = "UPDATE m_members SET search_count = search_count + 1 WHERE member_id IN ($placeholders)";
        $stmt = $pdo->prepare($updateSql);
        $stmt->execute($members);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Search</title>
<link rel="stylesheet" href="./styles/common.css">
<link rel="stylesheet" href="./styles/search.css">
</head>
<body id="page-top">
<h1>Search</h1>
<h2>パート → 楽器 → メンバーを選択して検索</h2>

<form method="GET" action="search.php">
  <div id="conditions">
    <div class="condition">
      <!-- パート -->
      <select name="conditions[0][part]" class="part">
        <option value="">-- パート --</option>
        <?php foreach ($parts as $part): ?>
        <option value="<?= $part['part_id'] ?>"><?= htmlspecialchars($part['part_name']) ?></option>
        <?php endforeach; ?>
      </select>

      <!-- 楽器 -->
      <select name="conditions[0][instrument]" class="instrument">
        <option value="">-- 楽器 --</option>
      </select>

      <!-- メンバー -->
      <select name="conditions[0][member]" class="member">
        <option value="">-- メンバー --</option>
      </select>
    </div>
  </div>

  <div class="btn-group">
    <button type="button" id="addCondition">＋追加</button>
    <button type="button" id="removeCondition">－削除</button>
  </div>

  <div class="search-btn-wrapper">
    <button type="submit">検索</button>
  </div>
</form>

<?php if (!empty($_GET)): ?>
<hr>
<h2>検索結果</h2>
<?php if (empty($results)): ?>
<p>該当する楽曲はありません</p>
<?php else: ?>
<?php foreach ($results as $row): ?>
<div class="song">
  <h3><strong><?= htmlspecialchars($row['song_name']) ?></strong>（<?= htmlspecialchars($row['work_title']) ?>）</h3>
</div>
<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>

<script>
// ===== パート→楽器 / 楽器→メンバー連動 =====
document.addEventListener('change', function(e) {
  // パート → 楽器
  if (e.target.classList.contains('part')) {
    const partId = e.target.value;
    const instrument = e.target.closest('.condition').querySelector('.instrument');
    const member = e.target.closest('.condition').querySelector('.member');
    instrument.innerHTML = '<option value="">-- 楽器 --</option>';
    member.innerHTML = '<option value="">-- メンバー --</option>';
    if (!partId) return;
    fetch('/ajax/ajax_instrument.php?part_id=' + partId)
      .then(res => res.text())
      .then(html => instrument.innerHTML += html);
  }

  // 楽器 → メンバー
  if (e.target.classList.contains('instrument')) {
    const instrumentId = e.target.value;
    const member = e.target.closest('.condition').querySelector('.member');
    member.innerHTML = '<option value="">-- メンバー --</option>';
    if (!instrumentId) return;
    fetch('/ajax/ajax_member.php?instrument_id=' + instrumentId)
      .then(res => res.text())
      .then(html => member.innerHTML += html);
  }
});

// ===== 条件追加/削除 =====
let index = 1;
const max = 5;
const conditionsContainer = document.getElementById('conditions');

document.getElementById('addCondition').addEventListener('click', () => {
  if (index >= max) return alert(`検索は最大${max}人までです`);
  const base = document.querySelector('.condition');
  const clone = base.cloneNode(true);
  clone.querySelectorAll('select').forEach(select => {
    select.name = select.name.replace(/\[\d+\]/, `[${index}]`);
    const first = select.options[0];
    select.innerHTML = '';
    select.appendChild(first);
    select.selectedIndex = 0;
  });
  conditionsContainer.appendChild(clone);
  index++;
});

document.getElementById('removeCondition').addEventListener('click', () => {
  const conditions = document.querySelectorAll('#conditions .condition');
  if (conditions.length <= 1) return alert('検索には最低1人は必要です');
  conditions[conditions.length - 1].remove();
  index--;
});

// ===== 送信時チェック =====
const form = document.querySelector('form');
form.addEventListener('submit', e => {
  const members = document.querySelectorAll('.member');
  const hasMember = [...members].some(m => m.value !== '');
  if (!hasMember) {
    alert('最低1人はメンバーを選択してください');
    e.preventDefault();
  }
});
</script>

</body>
</html>
