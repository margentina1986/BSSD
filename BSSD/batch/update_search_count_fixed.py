import mysql.connector
from mysql.connector import Error
import smtplib
from email.message import EmailMessage

# DB接続設定
db_config = {
    'host': 'localhost',
    'user': 'trainee',
    'password': 'password',
    'database': 'bssdtest',
    'charset': 'utf8'
}

# メール設定
MAIL_FROM = 'example@example.com'  # 送信元メール
MAIL_TO = 'example@example.com'                    # 受信先
MAIL_SUBJECT = 'Python Script Update Failure'

try:
    # DB接続
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()

    # 更新処理
    update_sql = """
        UPDATE m_members
        SET search_count_fixed = search_count
        WHERE is_display = 1
    """
    cursor.execute(update_sql)
    conn.commit()
    print(f"{cursor.rowcount} records updated successfully.")

except Error as e:
    print("Error:", e)

    # エラー発生時にメール送信（英語メッセージ）
    try:
        msg = EmailMessage()
        msg.set_content(f"update_search_count_fixed.py failed:\n{e}")  # 英語だけ
        msg['Subject'] = MAIL_SUBJECT
        msg['From'] = MAIL_FROM
        msg['To'] = MAIL_TO

        # さくらレンタルサーバー SMTP（STARTTLS）
        with smtplib.SMTP('example.example.ne.jp', 587, timeout=10) as smtp:
            smtp.starttls()
            smtp.login('example@example.com', 'password')  
            smtp.send_message(msg)

        print("Error email sent successfully.")

    except Exception as mail_err:
        print("Failed to send email:", mail_err)

finally:
    if 'cursor' in locals():
        cursor.close()
    if 'conn' in locals():
        conn.close()
