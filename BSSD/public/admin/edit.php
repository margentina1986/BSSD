<?php
require __DIR__ . '/../../config/db_connect.php';

/* 対象テーブル */
$table = $_GET['table'] ?? '';
$id    = $_GET['id'] ?? '';

$validTables = [
    'm_songs',
    'm_members',
    'm_parts',
    'm_instrument',
    'm_performances'
];

if (!in_array($table, $validTables) || !$id) {
    die('不正なアクセスです');
}

/* PK名をテーブルごとに定義 */
$pkMap = [
    'm_songs'        => 'song_id',
    'm_members'      => 'member_id',
    'm_parts'        => 'part_id',
    'm_instrument'   => 'instrument_id',
    'm_performances' => 'performance_id'
];
$pk = $pkMap[$table];

/* 対象レコード取得 */
$stmt = $pdo->prepare("SELECT * FROM {$table} WHERE {$pk} = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('データが存在しません');
}

$error = '';
$success = false;

/* 更新処理 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($table) {
            case 'm_songs':
                $song_name  = $_POST['song_name'] ?? '';
                $work_title = $_POST['work_title'] ?? '';
                if (!$song_name || !$work_title) {
                    throw new Exception('必須項目が未入力です');
                }

                $stmt = $pdo->prepare("
                    UPDATE m_songs
                    SET song_name = ?, work_title = ?
                    WHERE song_id = ?
                ");
                $stmt->execute([$song_name, $work_title, $id]);
                break;

            case 'm_members':
                $member_name     = $_POST['member_name'] ?? '';
                $member_hiragana = $_POST['member_hiragana'] ?? '';
                $member_katakana = $_POST['member_katakana'] ?? '';
                $member_alphabet = $_POST['member_alphabet'] ?? '';
                $member_other    = $_POST['member_other'] ?? '';
                $is_display      = isset($_POST['is_display']) ? 1 : 0;

                if (!$member_name || !$member_hiragana || !$member_katakana || !$member_alphabet) {
                    throw new Exception('必須項目が未入力です');
                }

                $stmt = $pdo->prepare("
                    UPDATE m_members
                    SET member_name = ?, member_hiragana = ?, member_katakana = ?,
                        member_alphabet = ?, member_other = ?, is_display = ?
                    WHERE member_id = ?
                ");
                $stmt->execute([
                    $member_name,
                    $member_hiragana,
                    $member_katakana,
                    $member_alphabet,
                    $member_other,
                    $is_display,
                    $id
                ]);
                break;

            case 'm_parts':
                $part_name = $_POST['part_name'] ?? '';
                if (!$part_name) {
                    throw new Exception('必須項目が未入力です');
                }

                $stmt = $pdo->prepare("
                    UPDATE m_parts
                    SET part_name = ?
                    WHERE part_id = ?
                ");
                $stmt->execute([$part_name, $id]);
                break;

            case 'm_instrument':
                $instrument_name = $_POST['instrument_name'] ?? '';
                $part_id         = $_POST['part_id'] ?? '';

                if (!$instrument_name || !$part_id) {
                    throw new Exception('必須項目が未入力です');
                }

                $stmt = $pdo->prepare("
                    UPDATE m_instrument
                    SET instrument_name = ?, part_id = ?
                    WHERE instrument_id = ?
                ");
                $stmt->execute([$instrument_name, $part_id, $id]);
                break;

            case 'm_performances':
                $song_id       = $_POST['song_id'] ?? '';
                $member_id     = $_POST['member_id'] ?? '';
                $instrument_id = $_POST['instrument_id'] ?? '';

                if (!$song_id || !$member_id || !$instrument_id) {
                    throw new Exception('必須項目が未入力です');
                }

                $stmt = $pdo->prepare("
                    UPDATE m_performances
                    SET song_id = ?, member_id = ?, instrument_id = ?
                    WHERE performance_id = ?
                ");
                $stmt->execute([$song_id, $member_id, $instrument_id, $id]);
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
<title>編集フォーム</title>
<link rel="stylesheet" href="styles/Admin.css">
</head>
<body>

<h1>編集フォーム（<?= htmlspecialchars($table) ?>）</h1>
<p>ID：<?= htmlspecialchars($id) ?></p>

<?php if ($success): ?>
    <p style="color:green;">更新が完了しました</p>
<?php elseif ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
<?php switch ($table):

case 'm_songs': ?>
    曲名：<input type="text" name="song_name" value="<?= htmlspecialchars($data['song_name']) ?>"><br>
    初回収録作品：<input type="text" name="work_title" value="<?= htmlspecialchars($data['work_title']) ?>"><br>
<?php break; ?>

<?php case 'm_members': ?>
    名前：<input type="text" name="member_name" value="<?= htmlspecialchars($data['member_name']) ?>"><br>
    ひらがな：<input type="text" name="member_hiragana" value="<?= htmlspecialchars($data['member_hiragana']) ?>"><br>
    カタカナ：<input type="text" name="member_katakana" value="<?= htmlspecialchars($data['member_katakana']) ?>"><br>
    アルファベット：<input type="text" name="member_alphabet" value="<?= htmlspecialchars($data['member_alphabet']) ?>"><br>
    その他：<input type="text" name="member_other" value="<?= htmlspecialchars($data['member_other']) ?>"><br>
    表示：<input type="checkbox" name="is_display" <?= $data['is_display'] ? 'checked' : '' ?>><br>
<?php break; ?>

<?php case 'm_parts': ?>
    楽器（親）名：<input type="text" name="part_name" value="<?= htmlspecialchars($data['part_name']) ?>"><br>
<?php break; ?>

<?php case 'm_instrument': ?>
    楽器名：<input type="text" name="instrument_name" value="<?= htmlspecialchars($data['instrument_name']) ?>"><br>
    親パート：
    <select name="part_id">
        <?php
        foreach ($pdo->query("SELECT * FROM m_parts") as $p) {
            $selected = ($p['part_id'] == $data['part_id']) ? 'selected' : '';
            echo "<option value='{$p['part_id']}' {$selected}>"
                 . htmlspecialchars($p['part_name']) . "</option>";
        }
        ?>
    </select><br>
<?php break; ?>

<?php case 'm_performances': ?>
    曲：
    <select name="song_id">
        <?php
        foreach ($pdo->query("SELECT * FROM m_songs") as $s) {
            $selected = ($s['song_id'] == $data['song_id']) ? 'selected' : '';
            echo "<option value='{$s['song_id']}' {$selected}>"
                 . htmlspecialchars($s['song_name']) . "</option>";
        }
        ?>
    </select><br>

    メンバー：
    <select name="member_id">
        <?php
        foreach ($pdo->query("SELECT * FROM m_members") as $m) {
            $selected = ($m['member_id'] == $data['member_id']) ? 'selected' : '';
            echo "<option value='{$m['member_id']}' {$selected}>"
                 . htmlspecialchars($m['member_name']) . "</option>";
        }
        ?>
    </select><br>

    楽器：
    <select name="instrument_id">
        <?php
        foreach ($pdo->query("SELECT * FROM m_instrument") as $i) {
            $selected = ($i['instrument_id'] == $data['instrument_id']) ? 'selected' : '';
            echo "<option value='{$i['instrument_id']}' {$selected}>"
                 . htmlspecialchars($i['instrument_name']) . "</option>";
        }
        ?>
    </select><br>
<?php break; ?>

<?php endswitch; ?>

<button type="submit">更新</button>
</form>

<p><a href="Admin.php">管理画面トップへ戻る</a></p>

</body>
</html>
