# Quick Database Fix - Manual Import Instructions

## Problem
Your database is missing the required tables (`settings`, `admin`, `history`, `track`).

## Solution - Import via Render Dashboard (EASIEST METHOD)

### Steps:

1. **Go to Render Dashboard**
   - Open: https://dashboard.render.com
   - Login to your account

2. **Open Your Database**
   - Click on your PostgreSQL database: `dbname_odlo_mxqy`
   - You should see database overview

3. **Connect via Web Terminal**
   - Click the **"Connect"** button (usually top-right)
   - Select **"External Connection"**
   - Click on **"PSQL Command"** option
   - A web-based terminal will open
   - Press **Enter** to connect

4. **Import the SQL**
   - The SQL file content is in: `db/import_postgres.sql`
   - **OPTION A**: Copy entire file contents and paste into terminal
   - **OPTION B**: Copy-paste the SQL below

5. **Verify Import**
   - After pasting, press Enter
   - You should see "INSERT" confirmations
   - Run: `SELECT COUNT(*) FROM admin;`
   - Should return: 1

---

## SQL TO PASTE (Complete Import Script)

```sql
-- PostgreSQL conversion of scrirvom_co database

-- Drop tables if they exist
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS history CASCADE;
DROP TABLE IF EXISTS settings CASCADE;
DROP TABLE IF EXISTS track CASCADE;
DROP TABLE IF EXISTS ocontrol CASCADE;

-- Table structure for table admin
CREATE TABLE admin (
  id SERIAL PRIMARY KEY,
  email VARCHAR(200) NOT NULL,
  password VARCHAR(200) NOT NULL
);

-- Dumping data for table admin
INSERT INTO admin (id, email, password) VALUES
(1, 'admin@digitalwebplus.com', 'admin123');

-- Table structure for table history
CREATE TABLE history (
  id SERIAL PRIMARY KEY,
  pname VARCHAR(200) NOT NULL,
  shipdate VARCHAR(100) NOT NULL,
  saddress VARCHAR(300) NOT NULL,
  sname VARCHAR(200) NOT NULL,
  raddress VARCHAR(300) NOT NULL,
  rname VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL,
  status VARCHAR(100) NOT NULL,
  location VARCHAR(200) NOT NULL,
  pdate VARCHAR(100) NOT NULL,
  pid VARCHAR(100) NOT NULL,
  edd VARCHAR(100) NOT NULL,
  weight VARCHAR(100) NOT NULL,
  servicetype VARCHAR(100) NOT NULL,
  pdesc VARCHAR(300) NOT NULL,
  qty VARCHAR(100) NOT NULL,
  image VARCHAR(300) NOT NULL,
  remark VARCHAR(500) NOT NULL
);

-- Dumping data for table history
INSERT INTO history (id, pname, shipdate, saddress, sname, raddress, rname, email, status, location, pdate, pid, edd, weight, servicetype, pdesc, qty, image, remark) VALUES
(45, 'samsung phone', '2020-06-14', 'abuja', 'Paul smith', 'Lagos Nigeria', 'Daniel Amos', 'ducanharry@gmail.com', 'In the warehouse', 'Ikeja warehouse', '2020-06-16', '8SHHHGGD63', '2020-06-30', '24kg', 'Persel', 'In good condotion', '2', 'Make Money fast Online.jpg', 'healthy delivery'),
(46, 'samsung phone', '', 'abuja', 'Paul smith', '', 'Daniel Amos', 'ducanharry@gmail.com', 'Custom check', 'Lagos', '2020-06-19', '8SHHHGGD63', '', '', '', '', '', '', 'Perfect condition'),
(47, 'samsung phone', '', 'abuja', 'Paul smith', '', 'Daniel Amos', 'ducanharry@gmail.com', 'On Hold', 'Delta state police', '2020-06-25', '8SHHHGGD63', '', '', '', '', '', '', 'Awaiting clearance  '),
(48, 'samsung phone', '', 'abuja', 'Paul smith', '', 'Daniel Amos', 'ducanharry@gmail.com', 'reuy5u', 'w4y53', '2020-07-25', '8SHHHGGD63', '', '', '', '', '', '', '5yu5u');

-- Table structure for table settings
CREATE TABLE settings (
  id SERIAL PRIMARY KEY,
  sname VARCHAR(200) NOT NULL,
  apipr VARCHAR(200) NOT NULL,
  apipu VARCHAR(200) NOT NULL,
  currency VARCHAR(200) NOT NULL,
  branch VARCHAR(200) NOT NULL,
  bname VARCHAR(200) NOT NULL,
  baddress VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL,
  phone VARCHAR(200) NOT NULL,
  title VARCHAR(200) NOT NULL,
  logo VARCHAR(100) NOT NULL
);

-- Dumping data for table settings
INSERT INTO settings (id, sname, apipr, apipu, currency, branch, bname, baddress, email, phone, title, logo) VALUES
(2, '', '', '', '$', '', 'McDan Logistics Ltd', '', 'support@mcdanlogistics.com', '+44 (74) 4144-3940', 'Welcome to McDan Logistics Ltd', '');

-- Table structure for table track
CREATE TABLE track (
  id SERIAL PRIMARY KEY,
  pname VARCHAR(200) NOT NULL,
  shipdate VARCHAR(100) NOT NULL,
  saddress VARCHAR(300) NOT NULL,
  sname VARCHAR(200) NOT NULL,
  raddress VARCHAR(300) NOT NULL,
  rname VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL,
  status VARCHAR(100) NOT NULL,
  location VARCHAR(200) NOT NULL,
  pdate VARCHAR(100) NOT NULL,
  pid VARCHAR(100) NOT NULL,
  edd VARCHAR(100) NOT NULL,
  weight VARCHAR(100) NOT NULL,
  servicetype VARCHAR(100) NOT NULL,
  pdesc VARCHAR(300) NOT NULL,
  qty VARCHAR(100) NOT NULL,
  image VARCHAR(300) NOT NULL,
  remark VARCHAR(500) NOT NULL
);

-- Dumping data for table track
INSERT INTO track (id, pname, shipdate, saddress, sname, raddress, rname, email, status, location, pdate, pid, edd, weight, servicetype, pdesc, qty, image, remark) VALUES
(28, 'samsung phone', '2020-06-14', 'abuja', 'Paul smith', 'Lagos Nigeria', 'Daniel Amos', 'ducanharry@gmail.com', 'deliver', 'Ikeja warehouse', '', '8SHHHGGD63', '2020-06-30', '24kg', 'Persel', 'In good condotion', '2', 'Computer Science.jpg', 'healthy delivery');

-- Table structure for table ocontrol
CREATE TABLE ocontrol (
  id SERIAL PRIMARY KEY,
  pid VARCHAR(100) NOT NULL,
  location VARCHAR(200) NOT NULL,
  remarks VARCHAR(200) NOT NULL,
  datee VARCHAR(100) NOT NULL
);

-- Reset sequences to proper values
SELECT setval('admin_id_seq', (SELECT MAX(id) FROM admin));
SELECT setval('history_id_seq', (SELECT MAX(id) FROM history));
SELECT setval('settings_id_seq', (SELECT MAX(id) FROM settings));
SELECT setval('track_id_seq', (SELECT MAX(id) FROM track));
```

---

## After Import - Test Login

**Admin Credentials:**
- Email: `admin@digitalwebplus.com`
- Password: `admin123`

**Verify Tables:**
```sql
SELECT COUNT(*) FROM admin;     -- Should return: 1
SELECT COUNT(*) FROM settings;  -- Should return: 1
SELECT COUNT(*) FROM history;   -- Should return: 4
SELECT COUNT(*) FROM track;     -- Should return: 1
```

---

## That's It!
Once imported, refresh your application and the errors should be gone.
