<?php
require __DIR__ . '/../config/db_connect.php';// DBへ接続

$recommendedMember = null;
$partsText = null;

// フォーム送信時のみ処理
if (!empty($_POST['birthday'])) {

    // 生年月日（YYYYMMDD）
    $birthday = $_POST['birthday'];

    // バリデーション（8桁数字のみ）
    if (preg_match('/^\d{8}$/', $birthday)) {

        // 今日の日付（YYYYMMDD）
        $today = date('Ymd');

        // 数値化（シンプルで再現性あり）
        $seed = intval($birthday) + intval($today);

        // 表示対象メンバー数を取得
        $countStmt = $pdo->query("
            SELECT COUNT(*) 
            FROM m_members 
            WHERE is_display = 1
        ");
        $memberCount = (int)$countStmt->fetchColumn();

        if ($memberCount > 0) {

            // 日替わり固定のインデックス
            $offset = $seed % $memberCount;

            // オススメメンバー取得
            $stmt = $pdo->prepare("
                SELECT member_id, member_name, member_other
                FROM m_members
                WHERE is_display = 1
                ORDER BY member_id
                LIMIT 1 OFFSET :offset
            ");
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $recommendedMember = $stmt->fetch(PDO::FETCH_ASSOC);

            // 担当楽器取得（複数可）
            if ($recommendedMember) {
                $partStmt = $pdo->prepare("
                    SELECT instrument_name
                    FROM (
                        SELECT i.instrument_name, i.instrument_id
                        FROM m_performances perf
                        JOIN m_instrument i ON perf.instrument_id = i.instrument_id
                        WHERE perf.member_id = :member_id
                        GROUP BY i.instrument_name, i.instrument_id
                    ) AS sub
                    ORDER BY instrument_id
                ");

                $partStmt->bindValue(':member_id', $recommendedMember['member_id'], PDO::PARAM_INT);
                $partStmt->execute();

                $instruments = $partStmt->fetchAll(PDO::FETCH_COLUMN);
                $partsText = implode(' / ', $instruments); // 変数名はそのまま使える
            }

        }
    }
}
?>
<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommend</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/recommend.css">
    <link rel="stylesheet" href="./styles/footer.css">
</head>

<body>
<nav id="menu"></nav>

<h1>Recommend</h1>
<h2>生年月日を入力すると、本日のオススメサポートミュージシャンが表示されます</h2>

<!-- 入力フォーム -->
<form method="post" class="recommend-input">
    <label for="birthday">あなたの生年月日（8桁）</label>
    <input
        type="text"
        id="birthday"
        name="birthday"
        inputmode="numeric"
        pattern="[0-9]{8}"
        maxlength="8"
        placeholder="19880921"
        required
    >
    <button type="submit">今日のオススメを見る</button>
</form>

<?php if ($recommendedMember): ?>
    <section class="recommend-result">
        <h3>本日のオススメミュージシャン</h3>

        <div class="recommend-card">
            <p class="recommend-name">
                <?= htmlspecialchars($recommendedMember['member_name'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <?php if (!empty($partsText)): ?>
                <p class="recommend-part">
                    <?= htmlspecialchars($partsText, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($recommendedMember['member_other'])): ?>
                <div class="recommend-other-section">
                    <h4 class="recommend-other-title">Other Projects</h4>
                    <p class="recommend-otherprojects">
                        <?= nl2br(htmlspecialchars($recommendedMember['member_other'], ENT_QUOTES, 'UTF-8')) ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
          <h3>どの曲を弾いているか、早速検索してみよう！</h3>
    </section>
<?php endif; ?>

<script src="scripts/menu.js"></script>
<?php include 'parts/footer.php'; ?>
</body>
</html>
