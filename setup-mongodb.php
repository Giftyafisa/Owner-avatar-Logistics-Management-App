#!/usr/bin/env php
<?php
/**
 * MongoDB Setup Script
 * Creates necessary directories and initializes default collections with data
 */

$appRoot = __DIR__;
$dataPath = $appRoot . '/data/mongodb/';

echo "<!DOCTYPE html><html><head><title>MongoDB Setup</title>
<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}
.success{color:green;background:#e8f5e9;padding:10px;margin:10px 0;border-radius:5px;}
.error{color:red;background:#ffebee;padding:10px;margin:10px 0;border-radius:5px;}
.info{color:#1976d2;background:#e3f2fd;padding:10px;margin:10px 0;border-radius:5px;}
h1{color:#333;}pre{background:#f5f5f5;padding:10px;overflow:auto;}</style></head><body>";
echo "<h1>🔧 MongoDB Setup</h1>";

// Create directories
if (!is_dir($dataPath)) {
    if (@mkdir($dataPath, 0777, true)) {
        echo "<div class='success'>✓ Created data directory: $dataPath</div>";
    } else {
        echo "<div class='error'>✗ Failed to create data directory. Check permissions.</div>";
    }
} else {
    echo "<div class='success'>✓ Data directory exists: $dataPath</div>";
}

// Initialize admin collection with default admin user
$adminData = array(
    array(
        'id' => '1',
        '_id' => '1',
        'email' => 'admin@digitalwebplus.com',
        'password' => 'admin123',
        'name' => 'Administrator',
        'role' => 'admin',
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    )
);

$adminFile = $dataPath . 'admin.json';
file_put_contents($adminFile, json_encode($adminData, JSON_PRETTY_PRINT));
echo "<div class='success'>✓ Created admin user: admin@digitalwebplus.com / admin123</div>";

// Initialize settings collection
$settingsData = array(
    array(
        'id' => '1',
        '_id' => '1',
        'bname' => 'Logistics Management',
        'title' => 'Logistics Management System',
        'logo' => 'assets/img/logo.png',
        'email' => 'admin@digitalwebplus.com',
        'phone' => '+1234567890',
        'baddress' => '123 Business Street',
        'branch' => 'Main Branch',
        'sname' => 'https://logistics.com',
        'currency' => 'USD',
        'apipu' => '',
        'apipr' => ''
    )
);

$settingsFile = $dataPath . 'settings.json';
file_put_contents($settingsFile, json_encode($settingsData, JSON_PRETTY_PRINT));
echo "<div class='success'>✓ Created default settings</div>";

// Initialize other required collections
$collections = array(
    'users' => array(),
    'history' => array(),
    'cards' => array(),
    'statements' => array(),
    'messages' => array(),
    'transactions' => array(),
    'track' => array(),
    'ocontrol' => array()
);

foreach ($collections as $name => $data) {
    $filePath = $dataPath . $name . '.json';
    if (!file_exists($filePath)) {
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
        echo "<div class='success'>✓ Created collection: $name</div>";
    } else {
        echo "<div class='info'>ℹ Collection already exists: $name</div>";
    }
}

echo "<h2>📋 Summary</h2>";
echo "<div class='success'><strong>Setup Complete!</strong></div>";
echo "<div class='info'>
<strong>Admin Login Credentials:</strong><br>
Email: <code>admin@digitalwebplus.com</code><br>
Password: <code>admin123</code><br><br>
<a href='admin/pages/login.php'>→ Go to Admin Login</a>
</div>";

echo "<h2>📁 Data Files Created</h2><pre>";
$files = scandir($dataPath);
foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
        $size = filesize($dataPath . $file);
        echo "$file - " . number_format($size) . " bytes\n";
    }
}
echo "</pre>";

echo "</body></html>";
?>
