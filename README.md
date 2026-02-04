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


## 各ページの説明と今後の更新予定
- **トップページ**（更新履歴含む）[top.html](BSSD/top.html)
- **このページについて**（軽い説明と挨拶文）  [about.html](BSSD/about.html)
- **検索機能**　データベースからプルダウン選択肢を取得し、検索結果を表示します。5人まで増やせて、AND検索を行います。 [search.php](BSSD/search.php)
- **ランキング**　Version2で追加予定。参加曲数のランキングと、検索された回数のランキングを表示します。毎日0時バッチです。 [ranking.html](BSSD/ranking.html)
- **オススメ機能**　Version2または3で追加予定。生年月日を入力すると、入力された数字と当日の日付を元に、本日のオススメメンバーが表示されます。 [recommend.html](BSSD/recommend.html)
- **Q&A**　想定質問と回答の表を貼ります。 [qa.html](BSSD/qa.html)
- **バージョン管理表**　このページのバージョン管理ではなく、各楽曲のバージョン違いをどう取り扱っているかの一覧表です。  [version.html](BSSD/version.html)
- **コンタクト機能**　Version2または3で追加予定。ユーザー⇒管理者への一方通行で、返信は行いません。文章のみのメッセージ機能と、jpeg・png・PDFを添付できる修正依頼機能の2種類を作ります。  [contact.html](BSSD/contact.html)
- **管理画面**　ここのみ要ログインで、CRUD機能とCSVの入出力を使用できます。  [Admin.html](BSSD/Admin.html)
