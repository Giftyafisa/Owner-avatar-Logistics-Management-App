import psycopg2
import sys

# Handle encoding
sys.stdout.reconfigure(encoding='utf-8')

# PostgreSQL connection parameters
host = 'dpg-d4h6rvumcj7s73brb53g-a.oregon-postgres.render.com'
port = '5432'
dbname = 'dbname_odlo'
user = 'dbuser'
password = 'IRPMRemSEj0V0Kj3lv2XmFEuN5gIY3dR'

# Connect to PostgreSQL
try:
    conn = psycopg2.connect(
        host=host,
        port=port,
        dbname=dbname,
        user=user,
        password=password
    )
    print("Connected to database successfully.")
except Exception as e:
    print(f"Connection failed: {e}")
    exit(1)

# Track ID and new status
track_id = 'SJH75GG9HH'
new_status = 'On Hold'

try:
    with conn.cursor() as cursor:
        # Check if track exists
        cursor.execute("SELECT id, status FROM track WHERE pid = %s", (track_id,))
        result = cursor.fetchone()

        if not result:
            print(f"Track ID {track_id} not found in database.")
            conn.close()
            exit(1)

        id, current_status = result
        print(f"Current status for Track ID {track_id}: {current_status}")

        # Update the status
        cursor.execute("UPDATE track SET status = %s WHERE id = %s", (new_status, id))
        conn.commit()

        print(f"Status updated successfully to: {new_status}")

        # Verify the update
        cursor.execute("SELECT status FROM track WHERE id = %s", (id,))
        verify_result = cursor.fetchone()
        print(f"Verified new status: {verify_result[0]}")

except Exception as e:
    print(f"Error: {e}")
    conn.rollback()

finally:
    conn.close()