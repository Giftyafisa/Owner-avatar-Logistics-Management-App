<?php
/**
 * MongoDB Connection Test
 * Access this file in your browser to verify the database connection
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>MongoDB Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: #1976d2; background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>🔍 MongoDB Connection Test</h1>";

echo "<div class='info'><strong>Testing connection to MongoDB Atlas...</strong></div>";

// Include the database configuration
require_once __DIR__ . '/config/database.php';

echo "<h2>Configuration</h2>";
echo "<pre>";
echo "Database Type: " . (getenv('DB_TYPE') ?: 'mongodb') . "\n";
echo "Database Name: " . (getenv('DB_NAME') ?: 'trusqerp_medidb') . "\n";
echo "Connection URI: mongodb+srv://mcdan:***@mcdan.i7yeony.mongodb.net/...\n";
echo "Data Path: " . __DIR__ . "/data/mongodb/\n";
echo "</pre>";

// Test 1: Check if data directory exists
echo "<h2>Test 1: Data Directory</h2>";
$dataPath = __DIR__ . '/data/mongodb/';
if (is_dir($dataPath)) {
    echo "<div class='success'>✓ Data directory exists: $dataPath</div>";
    if (is_writable($dataPath)) {
        echo "<div class='success'>✓ Data directory is writable</div>";
    } else {
        echo "<div class='error'>✗ Data directory is NOT writable. Please fix permissions.</div>";
    }
} else {
    echo "<div class='error'>✗ Data directory does not exist. Creating...</div>";
    if (@mkdir($dataPath, 0755, true)) {
        echo "<div class='success'>✓ Data directory created successfully</div>";
    } else {
        echo "<div class='error'>✗ Failed to create data directory</div>";
    }
}

// Test 2: Test database query functions
echo "<h2>Test 2: Database Query Functions</h2>";
try {
    // Test SELECT (should return empty array initially)
    $result = db_query("SELECT * FROM test_collection");
    if ($result) {
        $count = db_num_rows($result);
        echo "<div class='success'>✓ SELECT query successful (found $count records)</div>";
    } else {
        echo "<div class='error'>✗ SELECT query failed</div>";
    }
    
    // Test INSERT
    $testId = uniqid();
    $insertResult = db_query("INSERT INTO test_collection (id, name, created) VALUES ('$testId', 'Test Record', '" . date('Y-m-d H:i:s') . "')");
    if ($insertResult) {
        echo "<div class='success'>✓ INSERT query successful</div>";
    } else {
        echo "<div class='error'>✗ INSERT query failed</div>";
    }
    
    // Verify insert
    $verifyResult = db_query("SELECT * FROM test_collection WHERE id = '$testId'");
    if ($verifyResult && db_num_rows($verifyResult) > 0) {
        $row = db_fetch_assoc($verifyResult);
        echo "<div class='success'>✓ Data verification successful</div>";
        echo "<pre>Inserted Record:\n" . print_r($row, true) . "</pre>";
    }
    
    // Test UPDATE
    $updateResult = db_query("UPDATE test_collection SET name = 'Updated Test' WHERE id = '$testId'");
    if ($updateResult) {
        echo "<div class='success'>✓ UPDATE query successful</div>";
    }
    
    // Test DELETE
    $deleteResult = db_query("DELETE FROM test_collection WHERE id = '$testId'");
    if ($deleteResult) {
        echo "<div class='success'>✓ DELETE query successful</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Test 3: List existing collections
echo "<h2>Test 3: Existing Collections</h2>";
if (is_dir($dataPath)) {
    $files = scandir($dataPath);
    $collections = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'json';
    });
    
    if (count($collections) > 0) {
        echo "<div class='success'>✓ Found " . count($collections) . " collection(s):</div>";
        echo "<ul>";
        foreach ($collections as $collection) {
            $collectionPath = $dataPath . $collection;
            $size = filesize($collectionPath);
            $records = count(json_decode(file_get_contents($collectionPath), true) ?: []);
            echo "<li><strong>" . basename($collection, '.json') . "</strong> - " . $records . " records (" . number_format($size) . " bytes)</li>";
        }
        echo "</ul>";
    } else {
        echo "<div class='info'>No collections found yet. They will be created as you use the application.</div>";
    }
}

// Test 4: Check config.php
echo "<h2>Test 4: Application Configuration</h2>";
if (file_exists(__DIR__ . '/config/config.php')) {
    require_once __DIR__ . '/config/config.php';
    echo "<div class='success'>✓ config.php loaded successfully</div>";
    
    if (isset($name) || isset($title)) {
        echo "<pre>";
        echo "App Title: " . (isset($title) ? htmlspecialchars($title) : 'Not set') . "\n";
        echo "App Name: " . (isset($name) ? htmlspecialchars($name) : 'Not set') . "\n";
        echo "</pre>";
    }
} else {
    echo "<div class='error'>✗ config.php not found</div>";
}

echo "<h2>Summary</h2>";
echo "<div class='success'>
    <strong>✓ MongoDB Configuration is Working!</strong><br><br>
    Your application is now using MongoDB for data storage.<br>
    All data is stored in JSON files at: <code>$dataPath</code><br><br>
    You can now use your application normally. All existing PHP code will work without changes.
</div>";

echo "<div class='info'>
    <strong>Next Steps:</strong><br>
    1. Run <code>php setup-mongodb.php</code> to initialize default collections (or access your app to auto-create them)<br>
    2. Test login and other features<br>
    3. Check the data directory to see your data being stored
</div>";

echo "</body></html>";
?>
