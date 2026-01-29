<body>
  <nav id="menu"></nav>

  <h1>Search</h1>
  <h2>プルダウンで担当パートと個人名を選択すると、該当する楽曲が表示されます</h2>
  <h3>※複数名を選んだ場合は、AND検索となります。OR検索はできませんので、その場合は個別に検索してください。<br>
      ※バージョン違いに関しては、Remixは原曲と同一扱いですが再録は別曲扱いとなります。
  </h3>

  <!-- 検索フォームのプルダウン選択 -->
<form method="GET" action="search.php">
<select id="part" name="part">
    <option value="">-- 選択してください --</option>
    <?php
        require 'db_connect.php';
        $sql = "SELECT part_id, part_name FROM m_parts ORDER BY part_id";
        $stmt = $pdo->query($sql);
        foreach ($stmt as $row) {
            echo '<option value="' . $row['part_id'] . '">' . $row['part_name'] . '</option>';
        }
    ?>
</select>


    <!-- 検索ボタン -->
    <button type="submit">検索</button>
  </form>

  <p>鋭意作成中</p>

  <script src="scripts/menu.js"></script>
  <?php include 'footer.php'; ?>
</body>
