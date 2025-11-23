# Database Import Instructions for Render PostgreSQL

## Automatic Import (Recommended)

Run this command in your terminal (replace with your actual Render database credentials):

```bash
psql postgresql://dbuser:IRPMRemSEj0V0Kj3lvX2mFEuN5gIY3dR@dpg-d4h6rvumcj7s3brb53g-a.oregon-postgres.render.com/dbname_odlo < db/import_postgres.sql
```

## Manual Import via Render Dashboard

1. Go to your Render Dashboard: https://dashboard.render.com
2. Navigate to your PostgreSQL database service
3. Click on "PSQL Command" tab
4. Copy the PSQL connection command shown there
5. Open your terminal and paste that command to connect
6. Once connected, run:
   ```sql
   \i db/import_postgres.sql
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
