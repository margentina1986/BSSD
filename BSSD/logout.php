<?php
// セッション開始
session_start();

// セッションを破棄してログインページにリダイレクト
session_destroy();
header('Location: /private/login.php');
exit;
