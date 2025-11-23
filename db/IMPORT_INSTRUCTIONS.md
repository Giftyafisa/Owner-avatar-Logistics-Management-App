# Database Import Instructions for Render PostgreSQL

## Import via Render Dashboard (RECOMMENDED - Avoids SSL Issues)

### Method 1: Copy/Paste SQL (Easiest)

1. Go to your Render Dashboard: https://dashboard.render.com
2. Click on your PostgreSQL database: **dbname_odlo**
3. Click the **"Connect"** button, then select **"External Connection"**
4. Click on **"PSQL Command"** - this opens a web-based terminal
5. A terminal will open with connection info. Press Enter to connect.
6. Copy the contents of `db/import_postgres.sql` file
7. Paste the entire SQL content into the PSQL terminal and press Enter
8. Wait for "INSERT" confirmations - you should see successful inserts for all tables

### Method 2: Use Render Shell (Alternative)

1. Go to your Render Dashboard
2. Find your **web service** (owner-avatar-logistics)
3. Click **"Shell"** tab on the left sidebar
4. This opens a terminal inside your running container
5. Run these commands:
   ```bash
   apt-get update && apt-get install -y postgresql-client
   PGPASSWORD=$DB_PASS psql -h $DB_HOST -U $DB_USER -d $DB_NAME -f /var/www/html/db/import_postgres.sql
   ```

## What This Import Does

- Creates 4 tables: `admin`, `history`, `settings`, `track`
- Inserts sample data including:
  - Admin user: admin@digitalwebplus.com / admin123
  - Sample shipment tracking data
  - Default settings for "Fright Cargo"
- Sets up auto-increment sequences properly

## Verify Import Success

After importing, connect to your database and run:
```sql
SELECT COUNT(*) FROM admin;
SELECT COUNT(*) FROM history;
SELECT COUNT(*) FROM settings;
SELECT COUNT(*) FROM track;
```

You should see:
- admin: 1 row
- history: 4 rows
- settings: 1 row
- track: 1 row

## After Import

Visit your deployed app at https://owner-avatar-logistics.onrender.com and test:
- Admin login: /admin/pages/login.php
- Track a shipment: Use tracking ID `8SHHHGGD63`
