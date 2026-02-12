<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // IP制御のためセッション使用
require __DIR__ . '/../config/db_connect.php';

// パート一覧取得
$partStmt = $pdo->query("SELECT part_id, part_name FROM m_parts ORDER BY CAST(part_id AS UNSIGNED) ASC");
$parts = $partStmt->fetchAll(PDO::FETCH_ASSOC);

// 初期化
$results = [];
$conditions = [];
$members = [];

// 検索処理（POST時のみ）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conditions = $_POST['conditions'] ?? [];

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
        $userIp = $_SERVER['REMOTE_ADDR'];
        if (!isset($_SESSION['search_log'])) $_SESSION['search_log'] = [];
        if (!isset($_SESSION['search_log'][$userIp])) {
            $_SESSION['search_log'][$userIp] = [
                'last_time' => 0,
                'count_1min' => 0,
                'time_1min_start' => time()
            ];
        }

        $now = time();
        $log = &$_SESSION['search_log'][$userIp];
        $shouldCount = true;

        if ($now - $log['last_time'] < 1) $shouldCount = false;
        if ($now - $log['time_1min_start'] >= 60) {
            $log['count_1min'] = 0;
            $log['time_1min_start'] = $now;
        }
        if ($log['count_1min'] >= 20) $shouldCount = false;

        if ($shouldCount) {
            foreach ($conditions as $cond) {
                if (!empty($cond['member'])) $members[] = $cond['member'];
            }
            $members = array_unique($members);

            if ($members) {
                $placeholders = implode(',', array_fill(0, count($members), '?'));
                $updateSql = "UPDATE m_members SET search_count = search_count + 1 WHERE member_id IN ($placeholders)";
                $stmt = $pdo->prepare($updateSql);
                $stmt->execute($members);
            }

            $log['last_time'] = $now;
            $log['count_1min']++;
        }
    }

    // 検索結果と条件をセッションに保存してリダイレクト
    $_SESSION['last_search_results'] = $results;
    $_SESSION['last_search_conditions'] = $conditions;
    header('Location: search.php');
    exit;
}

// GET時はセッションから検索結果を取得
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $results = $_SESSION['last_search_results'] ?? [];
    $conditions = $_SESSION['last_search_conditions'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search</title>
<link rel="stylesheet" href="./styles/common.css">
<link rel="stylesheet" href="./styles/search.css">
<link rel="stylesheet" href="./styles/footer.css">
</head>
<script>
const savedConditions = <?= json_encode($conditions) ?>;
</script>
<body id="page-top">
<nav id="menu"></nav>
<main>
<h1>Search</h1>
<h2>パート → 楽器 → メンバーを選択して検索</h2>

<form method="POST" action="search.php">
<div id="conditions">
<div class="condition">
<select name="conditions[0][part]" class="part">
<option value="">-- パート --</option>
<?php foreach ($parts as $part): ?>
<option value="<?= $part['part_id'] ?>"><?= htmlspecialchars($part['part_name']) ?></option>
<?php endforeach; ?>
</select>
<select name="conditions[0][instrument]" class="instrument">
<option value="">-- 楽器 --</option>
</select>
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

<?php if (!empty($results)): ?>
<hr>
<h3>検索結果</h3>
<h4>曲名（収録作品）</h4>
<h5>※基本的に初回収録作品のみ表示されます※</h5>
<?php foreach ($results as $row): ?>
<div class="song">
<p class="content-text"><strong><?= htmlspecialchars($row['song_name']) ?></strong>（<?= htmlspecialchars($row['work_title']) ?>）</p>
</div>
<?php endforeach; ?>
<?php elseif (!empty($conditions)): ?>
<p class="content-text">該当する楽曲はありません</p>
<?php endif; ?>

<script>
// ここからは前回のJavaScriptそのまま
function getSelectedPairs() {
const pairs = [];
document.querySelectorAll('.condition').forEach(cond => {
const member = cond.querySelector('.member').value;
const instrument = cond.querySelector('.instrument').value;
if (member && instrument) pairs.push(instrument + '_' + member);
});
return pairs;
}

document.addEventListener('change', function(e) {
if (e.target.classList.contains('part')) {
const partId = e.target.value;
const instrument = e.target.closest('.condition').querySelector('.instrument');
const member = e.target.closest('.condition').querySelector('.member');
instrument.innerHTML = '<option value="">-- 楽器 --</option>';
member.innerHTML = '<option value="">-- メンバー --</option>';
if (!partId) return;
fetch('./ajax/ajax_instrument.php?part_id=' + partId)
.then(res => res.text())
.then(html => {
instrument.innerHTML += html;
const options = instrument.querySelectorAll('option:not([value=""])');
if (options.length === 1) {
instrument.value = options[0].value;
setTimeout(() => {
instrument.dispatchEvent(new Event('change', { bubbles: true }));
}, 0);
}
});
}
if (e.target.classList.contains('instrument')) {
const instrumentId = e.target.value;
const cond = e.target.closest('.condition');
const member = cond.querySelector('.member');
member.innerHTML = '<option value="">-- メンバー --</option>';
if (!instrumentId) return;
fetch('./ajax/ajax_member.php?instrument_id=' + instrumentId)
.then(res => res.text())
.then(html => {
const tmpDiv = document.createElement('div');
tmpDiv.innerHTML = html;
const selectedPairs = getSelectedPairs();
Array.from(tmpDiv.children).forEach(option => {
const pairKey = instrumentId + '_' + option.value;
if (!selectedPairs.includes(pairKey)) member.appendChild(option);
});
});
}
});

let index = 1;
const max = 5;
const conditionsContainer = document.getElementById('conditions');

document.getElementById('addCondition').addEventListener('click', () => {
if (index >= max) return alert(`検索は最大${max}人までです`);
const base = document.querySelector('.condition');
const clone = base.cloneNode(true);
clone.querySelectorAll('select').forEach(select => {
select.name = select.name.replace(/\[\d+\]/, `[${index}]`);
if (!select.classList.contains('part')) {
const first = select.options[0].cloneNode(true);
select.innerHTML = '';
select.appendChild(first);
select.selectedIndex = 0;
}
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

document.addEventListener('DOMContentLoaded', async () => {
if (!savedConditions.length) return;
for (let i = 1; i < savedConditions.length; i++) document.getElementById('addCondition').click();
const conditionEls = document.querySelectorAll('.condition');
for (let i = 0; i < savedConditions.length; i++) {
const cond = savedConditions[i];
const el = conditionEls[i];
if (cond.part) {
el.querySelector('.part').value = cond.part;
const instrumentRes = await fetch('./ajax/ajax_instrument.php?part_id=' + cond.part);
el.querySelector('.instrument').innerHTML += await instrumentRes.text();
}
if (cond.instrument) {
el.querySelector('.instrument').value = cond.instrument;
const memberRes = await fetch('./ajax/ajax_member.php?instrument_id=' + cond.instrument);
el.querySelector('.member').innerHTML += await memberRes.text();
}
if (cond.member) el.querySelector('.member').value = cond.member;
}
});

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

<script src="scripts/menu.js"></script>
</main>
<?php include 'parts/footer.php'; ?>
</body>
</html>
