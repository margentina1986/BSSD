# BSSD -B'z Support musician Search Database-

職業訓練校の卒業制作として開発した、B'zの各サポートメンバーがどの楽曲を演奏しているか検索できるデータベースサイトです。
ゼロからプログラミングを学んで3ヶ月時点での作品。個人開発、開発期間は約3週間。初めてのWebアプリ制作ですが、データベース設計からフロント・バックエンドまで一通り実装しています。

## 本番環境
http://bz-support-db.com

## 使用言語とツール
- フロントエンド：HTML / CSS / JavaScript
- バックエンド：PHP / MySQL
- バッチ処理：Python
- 開発環境：VS Code, DBeaver
- デプロイ先：さくらサーバー（本番環境）
- コーディング補助：ChatGPT,Copilot
- 画像生成：Gemini
---

## 各ページの説明
- **トップページ**（更新履歴含む）[index.php](BSSD/public/index.php)
- **このページについて**（サイトマップへのリンクと挨拶文）  [about.php](BSSD/public/about.php)
- **検索機能**　データベースからプルダウン選択肢を取得。最大5人までAND検索可能。 [search.php](BSSD/public/search.php)
- **オススメ機能**　生年月日から今日のおすすめメンバーを表示。 [recommend.php](BSSD/public/recommend.php)
- **ランキング**　参加曲数ランキング・検索回数ランキングを表示。（後者は毎日0時にバッチ更新） [ranking.php](BSSD/public/ranking.php)
- **Q&A**　想定質問と回答の一覧。 [qa.php](BSSD/public/qa.php)
- **バージョン管理表**　楽曲のバージョン違いまとめ。曲ごとのページへのリンク。  [version.php](BSSD/public/version.php)
- **コンタクト機能**　ユーザーから管理者へのメッセージ送信。添付ファイルはサーバ保存。  [contact.php](BSSD/public/contact.php)
- **管理画面**　CRUD機能・CSV入出力。ログイン制御あり。  [Admin.php](BSSD/public/admin/Admin.php)

## 機能ハイライト
- 多対多のデータベース設計（MySQL）
- 検索機能：プルダウン連動、複数条件AND検索
- ランキング自動更新：検索回数・参加曲数に応じてランキングを更新
- バッチ処理：失敗時に管理者へメール通知
- 管理画面：CRUD＋CSV入出力＋ログイン制御
- 問い合わせフォーム：メール送信／添付ファイル保存
- レスポンシブ対応：スマホ・タブレット対応
