<?php
/**
 * Database Import Script for PostgreSQL
 * This script imports the database schema and initial data
 */

// Load database configuration
require_once __DIR__ . '/config/database.php';

echo "====================================\n";
echo "Database Import Script\n";
echo "====================================\n\n";

// Check if we're using PostgreSQL
if ($db_type !== 'pgsql') {
    die("ERROR: This script is for PostgreSQL only. Current DB type: $db_type\n");
}

echo "Database Configuration:\n";
echo "- Host: $db_host\n";
echo "- User: $db_user\n";
echo "- Database: $db_name\n";
echo "- Type: $db_type\n\n";

// Read the SQL file
$sql_file = __DIR__ . '/db/import_postgres.sql';
if (!file_exists($sql_file)) {
    die("ERROR: SQL file not found at: $sql_file\n");
}

echo "Reading SQL file: $sql_file\n\n";
$sql_content = file_get_contents($sql_file);

// Split SQL into individual statements
$statements = array_filter(
    array_map('trim', explode(';', $sql_content)),
    function($stmt) {
        return !empty($stmt) && !preg_match('/^--/', $stmt);
    }
);

echo "Found " . count($statements) . " SQL statements to execute\n\n";
echo "Starting import...\n";
echo "------------------------------------\n";

$success_count = 0;
$error_count = 0;

foreach ($statements as $index => $statement) {
    if (empty(trim($statement))) continue;
    
    // Show first 60 characters of each statement
    $preview = substr(preg_replace('/\s+/', ' ', $statement), 0, 60);
    echo ($index + 1) . ". Executing: $preview...\n";
    
    $result = @pg_query($link, $statement);
    
    if ($result) {
        $success_count++;
        echo "   ✓ Success\n";
    } else {
        $error_count++;
        $error = pg_last_error($link);
        echo "   ✗ Error: $error\n";
    }
}

echo "------------------------------------\n";
echo "\nImport Summary:\n";
echo "- Successful: $success_count\n";
echo "- Failed: $error_count\n";
echo "- Total: " . count($statements) . "\n\n";

// Verify tables were created
echo "Verifying tables...\n";
$tables = ['admin', 'settings', 'history', 'track'];

foreach ($tables as $table) {
    $result = @pg_query($link, "SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = pg_fetch_assoc($result);
        echo "✓ Table '$table': {$row['count']} rows\n";
    } else {
        echo "✗ Table '$table': NOT FOUND\n";
    }
}

echo "\n====================================\n";
echo "Import Complete!\n";
echo "====================================\n";
echo "\nDefault Admin Credentials:\n";
echo "Email: admin@digitalwebplus.com\n";
echo "Password: admin123\n";
echo "====================================\n";

pg_close($link);
?>
