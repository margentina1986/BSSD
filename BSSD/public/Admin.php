<?php
// config 読み込み
require __DIR__ . '/../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    // テーブル名を POST に追加
    $_POST['table'] = $_POST['table'] ?? '';
    
    // public 外の import_csv.php を呼ぶ
    include __DIR__ . '/../csv/import_csv.php';
    
    // import_csv.php 側で JSON を echo しているのでここで終了
    exit;
}
?>

<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>管理画面</title>
<link href="styles/Admin.css" rel="stylesheet" />
</head>
<body>

<h1>管理画面</h1>

<h2>一括処理</h2>

<select name="job"><!--処理するテーブルをプルダウンで選択-->
    <option value="m_songs">楽曲マスタ</option>
    <option value="m_members">メンバーマスタ</option>
    <option value="m_parts">担当パートマスタ</option>
    <option value="m_instrument">担当楽器マスタ</option>
    <option value="m_performances">演奏曲マスタ</option>
</select>

<div class="btn-group">
    <button type="button" id="importBtn">CSVインポート</button>
    <button type="button" id="runBtn">取込処理実行</button>
</div>
<button type="button" id="exportBtn">CSVエクスポート</button>

<script>
let selectedFile = null;

// ファイル選択
document.getElementById('importBtn').addEventListener('click', function() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.csv';
    input.onchange = e => {
        const file = e.target.files[0];
        if (!file) return;
        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
            alert('CSVファイルを選択してください');
            selectedFile = null;
            return;
        }
        selectedFile = file;
        alert(`${file.name} を選択しました。取込処理実行ボタンを押してください。`);
    };
    input.click();
});

// CSV 取込処理
document.getElementById('runBtn').addEventListener('click', function() {
    const tableSelect = document.querySelector('select[name="job"]');
    const tableName = tableSelect.value;

    if (!selectedFile) {
        alert('ファイルが選択されていません');
        return;
    }
    if (!confirm(`${selectedFile.name} を取り込みますか？\nデータは全件上書きされます`)) return;

    const formData = new FormData();
    formData.append('csv_file', selectedFile);
    formData.append('table', tableName); // テーブル名を送る

    fetch('Admin.php', { 
    method: 'POST',
    body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('データベースの更新が完了しました');
        } else {
            alert('更新に失敗しました: ' + data.error);
        }
        selectedFile = null;
    })
    .catch(err => alert('通信エラー: ' + err));
});

// CSV ダウンロード
document.getElementById('exportBtn').addEventListener('click', function() {
    if (!confirm('データベースからCSVをダウンロードしますか？')) return;
    window.location.href = '../download_csv.php';
});
</script>

<h2>個別処理</h2>
<select name="job"><!--処理するテーブルをプルダウンで選択-->
    <option value="m_songs">楽曲マスタ</option>
    <option value="m_members">メンバーマスタ</option>
    <option value="m_parts">担当パートマスタ</option>
    <option value="m_instrument">担当楽器マスタ</option>
    <option value="m_performances">演奏曲マスタ</option>
</select>

<div class="btn-group"><!--選択されたテーブルのCRUDへ遷移　後ほどJavaScriptを作成-->
    <button type="button" onclick="location.href='遷移先URL'">一覧</button>
    <button type="button" onclick="location.href='遷移先URL'">追加</button>
    <button type="button" onclick="location.href='遷移先URL'">更新</button>
    <button type="button" onclick="location.href='遷移先URL'">削除</button>
</div>

</body>
</html>
