<?php
// セッション開始
session_start();

// ログイン済みのユーザーがアクセスした場合は管理画面にリダイレクト
if (isset($_SESSION['user_id'])) {
    header('Location: /public/admin/Admin.php');
    exit;
}

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送信されたユーザー名（DBユーザー）とパスワードを取得
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // ユーザー名とパスワードが入力されているか確認
    if (empty($username) || empty($password)) {
        $error = 'ユーザー名またはパスワードが入力されていません';
    } else {
        // データベース接続設定（db_connect.php から読み込み）
        $host = 'localhost';
        $dbname = 'bssdtest'; // データベース名（任意のデータベース）
        $charset = 'utf8mb4';

        // DB接続に使用するユーザー名とパスワード（フォームから送信されたもの）
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
            PDO::ATTR_EMULATE_PREPARES   => false,                 
        ];

        // 接続を試みる
        try {
            $pdo = new PDO($dsn, $username, $password, $options);
            // 接続成功 → ログイン成功
            $_SESSION['user_id'] = $username;  // ユーザー名をセッションに保存
            header('Location: /public/admin/Admin.php');  // 管理画面にリダイレクト
            exit;
        } catch (PDOException $e) {
            // 接続失敗 → ログイン失敗
            $error = 'ユーザー名またはパスワードが間違っています';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
</head>
<body>
    <h1>ログイン</h1>

    <?php if (isset($error)) : ?>
        <p style="color: red;"><?= $error; ?></p>
    <?php endif; ?>

    <form method="post">
        <div>
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>
