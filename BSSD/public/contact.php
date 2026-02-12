<?php
// Contactフォーム処理
$success = false;
$errors = [];
$honeypot = $_POST['contact_check'] ?? '';
$type = $_POST['contact_type'] ?? '';
$uploadDir = __DIR__ . '/../uploads'; // 公開ディレクトリ外

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // botチェック　隠しボタンにチェックを入れると送信不可
    if (!empty($honeypot)) {
        $errors[] = "送信できません";
    } else {
        $message = trim($_POST['message'] ?? '');
        $files = $_FILES['attachments'] ?? null;

        if (empty($message)) {
            $errors[] = "本文を入力してください";
        }

        $uploadedFiles = [];
        if ($type === 'fix' && $files) {
            if (count(array_filter($files['name'])) > 3) {
                $errors[] = "添付ファイルは3枚までです";
            }

            foreach ($files['name'] as $i => $name) {
                if (empty($name)) continue;
                $tmp = $files['tmp_name'][$i];
                $size = $files['size'][$i];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
                    $errors[] = "ファイル形式はjpg/png/pdfのみです";
                    continue;
                }
                if ($size > 3 * 1024 * 1024) {
                    $errors[] = "ファイルサイズは3MBまでです";
                    continue;
                }

                $newName = uniqid('file_', true) . '.' . $ext;
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (move_uploaded_file($tmp, "$uploadDir/$newName")) {
                    $uploadedFiles[] = $newName;
                } else {
                    $errors[] = "ファイルのアップロードに失敗しました";
                }
            }
        }

        if (empty($errors)) {
            $to = 'example@example.com'; // 送信先メール
            $subject = ($type === 'message') ? 'サイトからのメッセージ' : '修正依頼';
            $body = "メッセージ内容:\n$message\n\n";

            if ($type === 'fix' && $uploadedFiles) {
                $body .= "添付ファイル一覧:\n";
                foreach ($uploadedFiles as $f) {
                    $body .= "サーバーURL: https://yourdomain.com/uploads/$f\n";
                }
            }

            if (mail($to, $subject, $body)) {
                $success = true;
            } else {
                $errors[] = "メール送信に失敗しました。";
            }
        }
    }
}
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="./styles/contact.css">
    <link rel="stylesheet" href="./styles/footer.css">
</head>
<body>
    <nav id="menu"></nav>

    <main>
        <h1>Contact</h1>
        <h2>管理人宛にメッセージや不具合修正依頼を送ることができます。<br>
            ※返信は行っておりません<br>
            ※個人情報は記載しないでください
        </h2>

        <?php if ($success): ?>
            <p class="content-text">送信ありがとうございました。<br>
            メッセージの返信は行っておりませんのでご了承ください。</p>
        <?php else: ?>
            <?php if ($errors): ?>
                <ul>
                    <?php foreach($errors as $e) echo "<li>$e</li>"; ?>
                </ul>
            <?php endif; ?>

            <div class="tabs">
                <div class="tab active" data-tab="message">メッセージ</div>
                <div class="tab" data-tab="fix">修正依頼</div>
            </div>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="contact_type" id="contact_type" value="message">
                <div class="honeypot">
                    <label>チェック</label>
                    <input type="text" name="contact_check" value="">
                </div>

                <div class="tab-content active" id="tab-message">
                    <textarea name="message" rows="6" placeholder="メッセージを入力してください" required></textarea>
                </div>
                <div class="tab-content" id="tab-fix">
                    <textarea name="message" rows="6" placeholder="修正依頼内容を入力してください" required></textarea>
                    <div class="file-wrapper">
                        <label for="file-upload" class="custom-file-btn">
                            ファイルを選択
                        </label>
                        <span id="file-name" class="file-name">
                            選択されていません
                        </span>
                    </div>
                    <input type="file"
                        id="file-upload"
                        name="attachments[]"
                        multiple
                        accept=".jpg,.jpeg,.png,.pdf">
                    <p class="form-text">添付ファイル（jpg/png/pdf 最大3枚、3MBまで）:</p>
                </div>

                <button type="submit">送信</button>
            </form>
        <?php endif; ?>
    </main>

    <script src="scripts/menu.js"></script>
    <script>
        // タブ切替
        const tabs = document.querySelectorAll('.tab');
        const contents = document.querySelectorAll('.tab-content');
        const typeInput = document.getElementById('contact_type');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                contents.forEach(c => c.classList.remove('active'));
                document.getElementById('tab-' + tab.dataset.tab).classList.add('active');

                typeInput.value = tab.dataset.tab;
            });
        });
    </script>
    <script>
    const fileInput = document.getElementById('file-upload');
    const fileName = document.getElementById('file-name');

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length === 0) {
            fileName.textContent = "選択されていません";
        } else if (fileInput.files.length === 1) {
            fileName.textContent = fileInput.files[0].name;
        } else {
            fileName.textContent = fileInput.files.length + "個のファイルが選択されています";
        }
    });
    </script>


    <?php include 'parts/footer.php'; ?>
</body>
</html>
