# BSSD

職業訓練校の卒業制作を兼ねて、B'zの各サポートメンバーがどの楽曲を弾いているのか検索できるデータベースサイトを作成していきます。
初めてなので不明点ばかりですが、頑張ります。

## 使用言語
- HTML
- CSS
- PHP
- JavaScript
- MySQL
- Python（Version2で追加予定のバッチ処理で使用します）

  ツールはVS CodeとDBeaverを使用しています。
---


## 各ページの説明
- **トップページ**（更新履歴含む）[index.php](BSSD/public/index.php)
- **このページについて**（軽い説明と挨拶文）  [about.php](BSSD/public/about.php)
- **検索機能**　データベースからプルダウン選択肢を取得し、検索結果を表示します。5人まで増やせて、AND検索を行います。 [search.php](BSSD/public/search.php)
- **ランキング**　参加曲数のランキングと、検索された回数のランキングを表示します。毎日0時バッチです。 [ranking.php](BSSD/public/ranking.php)
- **オススメ機能**　生年月日を入力すると、入力された数字と当日の日付を元に、本日のオススメメンバーが表示されます。 [recommend.php](BSSD/public/recommend.php)
- **Q&A**　想定質問と回答です。 [qa.php](BSSD/public/qa.php)
- **バージョン管理表**　このページのバージョン管理ではなく、各楽曲のバージョン違いをどう取り扱っているかの一覧表です。  [version.php](BSSD/public/version.php)
- **コンタクト機能**　Version2または3で追加予定。ユーザー⇒管理者への一方通行で、返信は行いません。文章のみのメッセージ機能と、jpeg・png・PDFを添付できる修正依頼機能の2種類を作ります。  [contact.php](BSSD/public/contact.php)
- **管理画面**　CRUD機能とCSVの入出力を使用できます。ここのみログイン機能を付けます。  [Admin.php](BSSD/public/admin/Admin.php)
