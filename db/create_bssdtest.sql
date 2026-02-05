/* DB作成 */
DROP DATABASE IF EXISTS bssdtest;
CREATE DATABASE bssdtest CHARACTER SET utf8 COLLATE utf8_general_ci;
 
/* ユーザを作成 
DROP USER IF EXISTS trainee;
CREATE USER trainee IDENTIFIED BY 'password';*/

/* 権限付与 */
GRANT ALL PRIVILEGES ON bssdtest.* TO trainee;

/* AUTOCOMMIT無効 */
SET AUTOCOMMIT=0;

/* DB選択 */
USE bssdtest;

/* メンバーマスタ作成 */
CREATE TABLE m_members
(
    member_id       INT PRIMARY KEY,
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
    part_id   INT PRIMARY KEY NOT NULL,
    part_name VARCHAR(100) NOT NULL
);


/* 楽器（子）マスタ作成 */
CREATE TABLE m_instrument
(
    instrument_id   INT PRIMARY KEY NOT NULL,
    instrument_name VARCHAR(100) NOT NULL,
    part_id         INT NOT NULL,
    FOREIGN KEY (part_id) REFERENCES m_parts(part_id)
);

/* 楽曲マスタ作成 */
CREATE TABLE m_songs
(
	song_id INT PRIMARY KEY,
	song_name varchar(100)NOT NULL,
	work_title varchar(100)NOT NULL
);


/*演奏曲マスタ作成 */
CREATE TABLE m_performances
(
    performances_id  INT PRIMARY KEY NOT NULL,
    song_id         INT NOT NULL,
    member_id	INT NOT NULL,
    instrument_id	INT NOT NULL,
    FOREIGN KEY (song_id) REFERENCES m_songs(song_id),
    FOREIGN KEY (member_id) REFERENCES m_members(member_id),
    FOREIGN KEY (instrument_id) REFERENCES m_instrument(instrument_id)
);


/* メンバーマスタINSERT */
INSERT INTO m_members VALUES(1,'満園庄太郎','みつぞのしょうたろう','ミツゾノショウタロウ','Showtaro Mitsuzono','flow-war',0,0,1);
INSERT INTO m_members VALUES(2,'黒瀬蛙一','くろせかいち','クロセカイチ','Kaichi Kurose','flow-war',0,0,1);
INSERT INTO m_members VALUES(3,'小野塚晃','おのづかあきら','オノヅカアキラ','Akira Onozuka','DIMENSION',0,0,1);
INSERT INTO m_members VALUES(4,'勝田一樹','かつたかずき','カツタカズキ','Kazuki Katsuta','DIMENSION',0,0,1);
INSERT INTO m_members VALUES(5,'徳永暁人','とくながあきひと','トクナガアキヒト','Akihito Tokunaga','doa',0,0,1);
INSERT INTO m_members VALUES(6,'宇徳敬子','うとくけいこ','ウトクケイコ','Keiko Utoku','Mi-Ke、B.B.クィーンズ',0,0,1);
INSERT INTO m_members VALUES(7,'山木秀夫','やまきひでお','ヤマキヒデオ','Hideo Yamaki','SHŌGUN',0,0,1);
INSERT INTO m_members VALUES(8,'森朱美','もりあけみ','モリアケミ','Akemi Mori','',0,0,1);
INSERT INTO m_members VALUES(9,'明石昌夫','あかしまさお','あかしまさお','Masao Akashi','AMG',0,0,1);

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
INSERT INTO m_instrument VALUES(7,'サキソフォン',5);
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

/* 楽曲マスタINSERT */
INSERT INTO m_songs VALUES(1,'LOVE PHANTOM','18th Single LOVE PHANTOM');
INSERT INTO m_songs VALUES(2,'Liar! Liar!','23rd Single Liar! Liar!');
INSERT INTO m_songs VALUES(3,'銀の翼で翔べ','10th Album Brotherhood');
INSERT INTO m_songs VALUES(4,'スイマーよ!!','9th Album SURVIVE');
INSERT INTO m_songs VALUES(5,'ultra soul (Alternative Guitar Solo ver.)','12th Album GREEN');
INSERT INTO m_songs VALUES(6,'煌めく人','11th Album ELEVEN');
INSERT INTO m_songs VALUES(7,'Shower','9th Album SURVIVE');
INSERT INTO m_songs VALUES(8,'イルミネーション','23rd Album FYOP');
INSERT INTO m_songs VALUES(9,'F・E・A・R','10th Album Brotherhood');
INSERT INTO m_songs VALUES(10,'TOKYO DEVIL','11th Album ELEVEN');
INSERT INTO m_songs VALUES(11,'コブシヲニギレ','11th Album ELEVEN');
INSERT INTO m_songs VALUES(12,'扉','11th Album ELEVEN');

/* 演奏曲マスタINSERT */
INSERT INTO m_performances VALUES(1,1,6,5);
INSERT INTO m_performances VALUES(2,1,8,6);
INSERT INTO m_performances VALUES(3,2,5,1);
INSERT INTO m_performances VALUES(4,3,1,1);
INSERT INTO m_performances VALUES(5,3,2,2);
INSERT INTO m_performances VALUES(6,3,4,7);
INSERT INTO m_performances VALUES(7,4,5,1);
INSERT INTO m_performances VALUES(8,4,7,1);
INSERT INTO m_performances VALUES(9,5,5,1);
INSERT INTO m_performances VALUES(10,6,9,1);
INSERT INTO m_performances VALUES(11,6,2,2);
INSERT INTO m_performances VALUES(12,7,5,1);
INSERT INTO m_performances VALUES(13,7,7,2);
INSERT INTO m_performances VALUES(14,7,3,3);
INSERT INTO m_performances VALUES(15,8,5,1);
INSERT INTO m_performances VALUES(16,8,7,2);
INSERT INTO m_performances VALUES(17,8,3,3);
INSERT INTO m_performances VALUES(18,9,1,1);
INSERT INTO m_performances VALUES(19,9,2,2);
INSERT INTO m_performances VALUES(20,10,9,1);
INSERT INTO m_performances VALUES(21,10,2,2);
INSERT INTO m_performances VALUES(22,11,9,1);
INSERT INTO m_performances VALUES(23,11,2,2);
INSERT INTO m_performances VALUES(24,12,1,1);
INSERT INTO m_performances VALUES(25,12,2,2);

commit;

