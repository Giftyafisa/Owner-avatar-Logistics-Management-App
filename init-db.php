<?php
/**
 * Initialize MongoDB with Admin User and Settings
 * Run this once to set up the database
 */

// Include database configuration
require_once __DIR__ . '/config/database.php';

echo "🚀 Initializing MongoDB with admin user and settings...\n";

// Create admin user
$adminSql = "INSERT INTO admin (id, email, password, name, role) VALUES ('1', 'admin@digitalwebplus.com', 'admin123', 'System Administrator', 'admin')";
$adminResult = db_query($adminSql);

if ($adminResult) {
    echo "✅ Admin user created successfully!\n";
    echo "   Email: admin@digitalwebplus.com\n";
    echo "   Password: admin123\n";
} else {
    echo "⚠️ Admin user may already exist or there was an error\n";
}

// Create default settings
$settingsSql = "INSERT INTO settings (id, currency, bname, logo, email, phone, baddress, title, branch, sname, apipu, apipr) VALUES ('1', '$', 'Logistics Management System', 'logo.png', 'admin@digitalwebplus.com', '+1234567890', '123 Business Street', 'Logistics Management', 'Main Branch', 'LMS', '', '')";
$settingsResult = db_query($settingsSql);

if ($settingsResult) {
    echo "✅ Settings created successfully!\n";
} else {
    echo "⚠️ Settings may already exist or there was an error\n";
}

// Verify admin user exists
$checkSql = "SELECT * FROM admin WHERE email='admin@digitalwebplus.com'";
$checkResult = db_query($checkSql);

if ($checkResult && db_num_rows($checkResult) > 0) {
    $admin = db_fetch_assoc($checkResult);
    echo "✅ Admin verification successful!\n";
    echo "   ID: " . $admin['id'] . "\n";
    echo "   Email: " . $admin['email'] . "\n";
    echo "   Name: " . $admin['name'] . "\n";
} else {
    echo "❌ Admin verification failed!\n";
}

// Verify settings exist
$checkSettingsSql = "SELECT * FROM settings";
$settingsCheckResult = db_query($checkSettingsSql);

if ($settingsCheckResult && db_num_rows($settingsCheckResult) > 0) {
    $settings = db_fetch_assoc($settingsCheckResult);
    echo "✅ Settings verification successful!\n";
    echo "   Company: " . $settings['bname'] . "\n";
    echo "   Email: " . $settings['email'] . "\n";
} else {
    echo "❌ Settings verification failed!\n";
}

echo "\n🎉 Initialization complete!\n";
echo "You can now login at: /admin/pages/login.php\n";
echo "Email: admin@digitalwebplus.com\n";
echo "Password: admin123\n";
?>