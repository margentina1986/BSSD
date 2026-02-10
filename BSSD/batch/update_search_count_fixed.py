# update_search_count_fixed.py
import mysql.connector
from mysql.connector import Error

# DB接続設定
db_config = {
    'host': 'localhost',
    'user': 'trainee',
    'password': 'password',
    'database': 'bssdtest',
    'charset': 'utf8'
}

try:
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()

    # search_count_fixedをsearch_countで上書き
    update_sql = """
        UPDATE m_members
        SET search_count_fixed = search_count
        WHERE is_display = 1
    """
    cursor.execute(update_sql)
    conn.commit()
    print(f"{cursor.rowcount}件を更新しました。")

except Error as e:
    print("Error:", e)

finally:
    if cursor:
        cursor.close()
    if conn:
        conn.close()
