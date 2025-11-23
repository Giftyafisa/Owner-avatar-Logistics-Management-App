-- PostgreSQL conversion of scrirvom_co database

-- Drop tables if they exist
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS history CASCADE;
DROP TABLE IF EXISTS settings CASCADE;
DROP TABLE IF EXISTS track CASCADE;

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
  pname VARCHAR(100) NOT NULL,
  shipdate VARCHAR(100) NOT NULL,
  saddress VARCHAR(200) NOT NULL,
  sname VARCHAR(200) NOT NULL,
  raddress VARCHAR(200) NOT NULL,
  rname VARCHAR(200) NOT NULL,
  email VARCHAR(100) NOT NULL,
  status VARCHAR(100) NOT NULL,
  location VARCHAR(100) NOT NULL,
  pdate VARCHAR(100) NOT NULL,
  pid VARCHAR(100) NOT NULL,
  edd VARCHAR(100) NOT NULL,
  weight VARCHAR(100) NOT NULL,
  servicetype VARCHAR(100) NOT NULL,
  pdesc VARCHAR(100) NOT NULL,
  qty VARCHAR(100) NOT NULL,
  image VARCHAR(100) NOT NULL,
  remark VARCHAR(100) NOT NULL
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
  pname VARCHAR(100) NOT NULL,
  shipdate VARCHAR(100) NOT NULL,
  saddress VARCHAR(200) NOT NULL,
  sname VARCHAR(200) NOT NULL,
  raddress VARCHAR(200) NOT NULL,
  rname VARCHAR(200) NOT NULL,
  email VARCHAR(100) NOT NULL,
  status VARCHAR(100) NOT NULL,
  location VARCHAR(100) NOT NULL,
  pdate VARCHAR(100) NOT NULL,
  pid VARCHAR(100) NOT NULL,
  edd VARCHAR(100) NOT NULL,
  weight VARCHAR(100) NOT NULL,
  servicetype VARCHAR(100) NOT NULL,
  pdesc VARCHAR(100) NOT NULL,
  qty VARCHAR(100) NOT NULL,
  image VARCHAR(100) NOT NULL,
  remark VARCHAR(100) NOT NULL
);

-- Dumping data for table track
INSERT INTO track (id, pname, shipdate, saddress, sname, raddress, rname, email, status, location, pdate, pid, edd, weight, servicetype, pdesc, qty, image, remark) VALUES
(28, 'samsung phone', '2020-06-14', 'abuja', 'Paul smith', 'Lagos Nigeria', 'Daniel Amos', 'ducanharry@gmail.com', 'deliver', 'Ikeja warehouse', '', '8SHHHGGD63', '2020-06-30', '24kg', 'Persel', 'In good condotion', '2', 'Computer Science.jpg', 'healthy delivery');

-- Reset sequences to match the data
SELECT setval('admin_id_seq', (SELECT MAX(id) FROM admin));
SELECT setval('history_id_seq', (SELECT MAX(id) FROM history));
SELECT setval('settings_id_seq', (SELECT MAX(id) FROM settings));
SELECT setval('track_id_seq', (SELECT MAX(id) FROM track));
