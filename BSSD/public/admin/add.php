<?php
require __DIR__ . '/../../config/db_connect.php';

// 対象テーブル取得
$table = $_GET['table'] ?? '';
$validTables = ['m_songs', 'm_members', 'm_parts', 'm_instrument', 'm_performances'];

if (!in_array($table, $validTables, true)) {
    die('不正なテーブル指定です');
}

// テーブルごとの主キー定義
$primaryKeys = [
    'm_songs'        => 'song_id',
    'm_members'      => 'member_id',
    'm_parts'        => 'part_id',
    'm_instrument'   => 'instrument_id',
    'm_performances' => 'performance_id',
];

// 次のIDを取得（表示用）
$pk = $primaryKeys[$table];
$stmt = $pdo->query("SELECT MAX($pk) AS max_id FROM $table");
$next_id = ((int)$stmt->fetch(PDO::FETCH_ASSOC)['max_id']) + 1;

// エラーメッセージ格納
$error = '';
$success = false;

// POST送信時
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($table) {

            case 'm_songs':
                $song_name  = $_POST['song_name'] ?? '';
                $work_title = $_POST['work_title'] ?? '';
                if ($song_name === '' || $work_title === '') {
                    throw new Exception('必須項目が未入力です');
                }

                $stmt = $pdo->prepare(
                    "INSERT INTO m_songs (song_name, work_title) VALUES (?, ?)"
                );
                $stmt->execute([$song_name, $work_title]);
                break;

            case 'm_members':
                $member_name     = $_POST['member_name'] ?? '';
                $member_hiragana = $_POST['member_hiragana'] ?? '';
                $member_katakana = $_POST['member_katakana'] ?? '';
                $member_alphabet = $_POST['member_alphabet'] ?? '';
                $member_other    = $_POST['member_other'] ?? '';
                $is_display      = isset($_POST['is_display']) ? 1 : 0;

                if ($member_name === '' || $member_hiragana === '' || $member_katakana === '' || $member_alphabet === '') {
                    throw new Exception('必須項目が未入力です');
                }

                $stmt = $pdo->prepare(
                    "INSERT INTO m_members
                     (member_name, member_hiragana, member_katakana, member_alphabet, member_other, search_count, search_count_fixed, is_display)
                     VALUES (?, ?, ?, ?, ?, 0, 0, ?)"
                );
                $stmt->execute([$member_name, $member_hiragana, $member_katakana, $member_alphabet, $member_other, $is_display]);
                break;

            case 'm_parts':
                $part_name = $_POST['part_name'] ?? '';
                if ($part_name === '') throw new Exception('必須項目が未入力です');

                $stmt = $pdo->prepare("INSERT INTO m_parts (part_name) VALUES (?)");
                $stmt->execute([$part_name]);
                break;

            case 'm_instrument':
                $instrument_name = $_POST['instrument_name'] ?? '';
                $part_id         = $_POST['part_id'] ?? '';
                if ($instrument_name === '' || $part_id === '') {
                    throw new Exception('必須項目が未入力です');
                }

                $stmt = $pdo->prepare(
                    "INSERT INTO m_instrument (instrument_name, part_id) VALUES (?, ?)"
                );
                $stmt->execute([$instrument_name, $part_id]);
                break;

            case 'm_performances':
                $song_id       = $_POST['song_id'] ?? '';
                $member_id     = $_POST['member_id'] ?? '';
                $instrument_id = $_POST['instrument_id'] ?? '';
                if ($song_id === '' || $member_id === '' || $instrument_id === '') {
                    throw new Exception('必須項目が未入力です');
                }

                $stmt = $pdo->prepare(
                    "INSERT INTO m_performances (song_id, member_id, instrument_id) VALUES (?, ?, ?)"
                );
                $stmt->execute([$song_id, $member_id, $instrument_id]);
                break;
        }

        $success = true;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>追加フォーム</title>
<link rel="stylesheet" href="../styles/Admin.css">
</head>
<body>

<h1>追加フォーム（<?= htmlspecialchars($table) ?>）</h1>

<p><strong>次のID：</strong> <?= htmlspecialchars($next_id) ?></p>

<?php if ($success): ?>
    <p style="color:green;">追加が完了しました</p>
<?php elseif ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
<?php switch ($table):

case 'm_songs': ?>
    曲名：<input type="text" name="song_name"><br>
    初回収録作品：<input type="text" name="work_title"><br>
<?php break;

case 'm_members': ?>
    名前：<input type="text" name="member_name"><br>
    ひらがな：<input type="text" name="member_hiragana"><br>
    カタカナ：<input type="text" name="member_katakana"><br>
    アルファベット：<input type="text" name="member_alphabet"><br>
    その他：<input type="text" name="member_other"><br>
    表示：<input type="checkbox" name="is_display" checked><br>
<?php break;

case 'm_parts': ?>
    パート名：<input type="text" name="part_name"><br>
<?php break;

case 'm_instrument': ?>
    楽器名：<input type="text" name="instrument_name"><br>
    親パート：
    <select name="part_id">
        <?php
        foreach ($pdo->query("SELECT part_id, part_name FROM m_parts") as $p) {
            echo '<option value="'.$p['part_id'].'">'.htmlspecialchars($p['part_name']).'</option>';
        }
        ?>
    </select><br>
<?php break;

case 'm_performances': ?>
    曲：
    <select name="song_id">
        <?php
        foreach ($pdo->query("SELECT song_id, song_name FROM m_songs") as $s) {
            echo '<option value="'.$s['song_id'].'">'.htmlspecialchars($s['song_name']).'</option>';
        }
        ?>
    </select><br>
    メンバー：
    <select name="member_id">
        <?php
        foreach ($pdo->query("SELECT member_id, member_name FROM m_members") as $m) {
            echo '<option value="'.$m['member_id'].'">'.htmlspecialchars($m['member_name']).'</option>';
        }
        ?>
    </select><br>
    楽器：
    <select name="instrument_id">
        <?php
        foreach ($pdo->query("SELECT instrument_id, instrument_name FROM m_instrument") as $i) {
            echo '<option value="'.$i['instrument_id'].'">'.htmlspecialchars($i['instrument_name']).'</option>';
        }
        ?>
    </select><br>
<?php break;

endswitch; ?>

<button type="submit">追加</button>
</form>

<p><a href="Admin.php">管理画面トップへ戻る</a></p>

</body>
</html>
