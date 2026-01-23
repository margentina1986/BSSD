// メニュー項目を配列で定義
const menuItems = [
    { name: "Top", link: "top.html" },
    { name: "About This", link: "about.html" },
    { name: "Search", link: "search.html" },
    { name: "Recommend", link: "recommend.html" },
    { name: "Ranking", link: "ranking.html" },
    { name: "Q & A", link: "qa.html" },
    { name: "Contact", link: "contact.html" }
];

// nav要素を取得
const nav = document.getElementById("menu");

// ul要素を作成
const ul = document.createElement("ul");

// 配列をループして li > a を作成
menuItems.forEach(item => {
    const li = document.createElement("li");
    const a = document.createElement("a");
    a.textContent = item.name;   // 表示文字
    a.href = item.link;          // リンク先
    li.appendChild(a);
    ul.appendChild(li);
});

// 完成した ul を nav に差し込む
nav.appendChild(ul);