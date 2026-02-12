/* DB作成 */
DROP DATABASE IF EXISTS bssd;
CREATE DATABASE bssd CHARACTER SET utf8 COLLATE utf8_general_ci;
 
/* ユーザを作成 
DROP USER IF EXISTS trainee;
CREATE USER trainee IDENTIFIED BY 'password';*/

/* 権限付与 */
GRANT ALL PRIVILEGES ON bssd.* TO trainee;

/* AUTOCOMMIT無効 */
SET AUTOCOMMIT=0;

/* DB選択 */
USE bssd;

/* メンバーマスタ作成 */
CREATE TABLE m_members
(
    member_id       INT PRIMARY KEY AUTO_INCREMENT,
    member_name     VARCHAR(100) NOT NULL,
    member_hiragana VARCHAR(100) NOT NULL,
    member_katakana VARCHAR(100) NOT NULL,
    member_alphabet VARCHAR(100) NOT NULL,
    member_other    VARCHAR(200),
    search_count    INT UNSIGNED NOT NULL,
    search_count_fixed INT UNSIGNED NOT NULL,
    is_display      TINYINT(1) NOT NULL
);


/* 楽器（親）マスタ作成 */
CREATE TABLE m_parts
(
    part_id   INT PRIMARY KEY AUTO_INCREMENT,
    part_name VARCHAR(100) NOT NULL
);


/* 楽器（子）マスタ作成 */
CREATE TABLE m_instrument
(
    instrument_id   INT PRIMARY KEY AUTO_INCREMENT,
    instrument_name VARCHAR(100) NOT NULL,
    part_id         INT NOT NULL,
    FOREIGN KEY (part_id) REFERENCES m_parts(part_id)
);

/* 楽曲マスタ作成 */
CREATE TABLE m_songs
(
	song_id INT PRIMARY KEY AUTO_INCREMENT,
	song_name varchar(100)NOT NULL,
	work_title varchar(100)NOT NULL
);


/*演奏曲マスタ作成 */
CREATE TABLE m_performances
(
    performance_id  INT PRIMARY KEY AUTO_INCREMENT,
    song_id         INT NOT NULL,
    member_id	INT NOT NULL,
    instrument_id	INT NOT NULL,
    FOREIGN KEY (song_id) REFERENCES m_songs(song_id),
    FOREIGN KEY (member_id) REFERENCES m_members(member_id),
    FOREIGN KEY (instrument_id) REFERENCES m_instrument(instrument_id)
);

/* 楽器（親）マスタINSERT */
INSERT INTO m_parts VALUES(1,'ベース');
INSERT INTO m_parts VALUES(2,'ドラム&パーカッション');
INSERT INTO m_parts VALUES(3,'キーボード');
INSERT INTO m_parts VALUES(4,'コーラス');
INSERT INTO m_parts VALUES(5,'ホーンセクション');
INSERT INTO m_parts VALUES(6,'ストリングス');
INSERT INTO m_parts VALUES(7,'その他');

/* 楽器（子）マスタINSERT */
INSERT INTO m_instrument VALUES(1,'ベース',1);
INSERT INTO m_instrument VALUES(2,'ドラム&パーカッション',2);
INSERT INTO m_instrument VALUES(3,'キーボード',3);
INSERT INTO m_instrument VALUES(4,'コーラス',4);
INSERT INTO m_instrument VALUES(5,'ボイス',4);
INSERT INTO m_instrument VALUES(6,'オペラ',4);
INSERT INTO m_instrument VALUES(7,'サックス',5);
INSERT INTO m_instrument VALUES(8,'トランペット',5);
INSERT INTO m_instrument VALUES(9,'トロンボーン',5);
INSERT INTO m_instrument VALUES(10,'ヴァイオリン',6);
INSERT INTO m_instrument VALUES(11,'ヴィオラ',6);
INSERT INTO m_instrument VALUES(12,'チェロ',6);
INSERT INTO m_instrument VALUES(13,'フルート',7);
INSERT INTO m_instrument VALUES(14,'ホルン',7);
INSERT INTO m_instrument VALUES(15,'オーボエ',7);
INSERT INTO m_instrument VALUES(16,'ブルースハープ',7);
INSERT INTO m_instrument VALUES(17,'チャイニーズゴング',7);
INSERT INTO m_instrument VALUES(18,'ギター',7);

commit;

