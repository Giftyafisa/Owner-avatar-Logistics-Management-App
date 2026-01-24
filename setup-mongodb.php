#!/usr/bin/env php
<?php
/**
 * MongoDB Setup Script
 * Creates necessary directories and initializes default collections
 */

$appRoot = dirname(__DIR__);
$dataPath = $appRoot . '/data/mongodb/';

// Create directories
if (!is_dir($dataPath)) {
    if (@mkdir($dataPath, 0755, true)) {
        echo "✓ Created data directory: $dataPath\n";
    } else {
        echo "✗ Failed to create data directory. Ensure permissions allow writing.\n";
        exit(1);
    }
} else {
    echo "✓ Data directory exists: $dataPath\n";
}

// Create default collections
$defaultCollections = [
    'users' => [],
    'settings' => [],
    'history' => [],
    'cards' => [],
    'statements' => [],
    'messages' => [],
    'transactions' => [],
];

foreach ($defaultCollections as $collection => $initialData) {
    $filePath = $dataPath . $collection . '.json';
    
    if (!file_exists($filePath)) {
        file_put_contents($filePath, json_encode($initialData, JSON_PRETTY_PRINT));
        echo "✓ Created collection: $collection.json\n";
    } else {
        echo "✓ Collection exists: $collection.json\n";
    }
}

echo "\n✓ MongoDB setup completed successfully!\n";
echo "Data is stored in: $dataPath\n";
echo "\nYou can now use the application with MongoDB backend.\n";
?>
