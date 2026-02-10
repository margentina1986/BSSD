<?php
// lib/recommend_logic.php

/**
 * 今日のオススメメンバーを取得する
 * 
 * @param PDO $pdo PDO接続オブジェクト
 * @param string $birthdate 生年月日（8桁文字列: YYYYMMDD）
 * @return array|null オススメメンバー情報（m_membersの連想配列）またはNULL
 */
function getTodayRecommendation(PDO $pdo, string $birthdate): ?array {
    // 当日の日付（YYYYMMDD）
    $today = date('Ymd');

    // 8桁+8桁の文字列 → 数字に変換
    $seedNumber = intval($birthdate . $today);

    // m_performancesの曲ID最大値を取得
    $stmt = $pdo->query("SELECT MAX(performance_id) AS max_id FROM m_performances");
    $maxPerformanceId = (int) $stmt->fetchColumn();

    if ($maxPerformanceId === 0) return null; // データなし

    // performance_idをseedNumberだけループ
    // %で循環させて範囲内に収める（1〜maxPerformanceId）
    $targetPerformanceId = ($seedNumber % $maxPerformanceId) + 1;

    // そのperformance_idのmember_idを取得
    $stmt = $pdo->prepare("SELECT member_id FROM m_performances WHERE performance_id = :pid LIMIT 1");
    $stmt->bindValue(':pid', $targetPerformanceId, PDO::PARAM_INT);
    $stmt->execute();
    $memberId = $stmt->fetchColumn();

    if (!$memberId) return null; // 何も見つからなかった場合

    // m_membersから詳細情報取得
    $stmt = $pdo->prepare("SELECT * FROM m_members WHERE member_id = :mid LIMIT 1");
    $stmt->bindValue(':mid', $memberId, PDO::PARAM_INT);
    $stmt->execute();
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    return $member ?: null;
}
