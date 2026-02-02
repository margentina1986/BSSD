<?php
// 接続設定
$host = 'localhost';
$dbname = 'bssdtest'; // データベース名
$user = 'your_username';  // ユーザー名
$pass = 'your_password';  // パスワード
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // エラー時に例外を投げる
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 結果を連想配列で取得
    PDO::ATTR_EMULATE_PREPARES   => false,                 // SQLインジェクション対策
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "接続成功！"; 
} catch (PDOException $e) {
    // 接続失敗時の処理
    exit('データベース接続に失敗しました：' . $e->getMessage());
}
?>
