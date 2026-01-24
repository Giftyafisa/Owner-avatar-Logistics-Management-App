<?php
/**
 * Database Configuration - MongoDB Atlas Connection
 * This file connects to the REAL MongoDB Atlas database
 */

// MongoDB Atlas connection settings
$mongodb_uri = getenv('DB_URI') ?: getenv('MONGODB_URI') ?: 'mongodb+srv://mcdan:11221122Mc@mcdan.i7yeony.mongodb.net/?retryWrites=true&w=majority&appName=mcdan';
$mongodb_database = getenv('DB_NAME') ?: getenv('MONGODB_DATABASE') ?: 'logistics_db';

// Global MongoDB connection
$mongoClient = null;
$mongoDatabase = null;

try {
    // Load the MongoDB library
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Create MongoDB client
    $mongoClient = new MongoDB\Client($mongodb_uri);
    $mongoDatabase = $mongoClient->selectDatabase($mongodb_database);
    
    // Test connection (using admin database for ping)
    $mongoClient->selectDatabase('admin')->command(['ping' => 1]);
    
} catch (Exception $e) {
    error_log("MongoDB Connection Error: " . $e->getMessage());
    // Don't die, allow graceful fallback
}

/**
 * Execute a SQL-like query against MongoDB
 */
function db_query($sql) {
    global $mongoDatabase;
    
    if (!$mongoDatabase) {
        error_log("MongoDB not connected. Query: $sql");
        return false;
    }
    
    try {
        // Parse SELECT query
        if (preg_match('/^SELECT\s+\*\s+FROM\s+(\w+)(?:\s+WHERE\s+(.+))?$/i', trim($sql), $matches)) {
            $collection = $matches[1];
            $where = isset($matches[2]) ? $matches[2] : '';
            
            $filter = parseWhereClause($where);
            $cursor = $mongoDatabase->selectCollection($collection)->find($filter);
            
            return new MongoDBResult($cursor->toArray());
        }
        
        // Parse INSERT query
        if (preg_match('/^INSERT\s+INTO\s+(\w+)\s*\(([^)]+)\)\s*VALUES\s*\((.+)\)$/is', trim($sql), $matches)) {
            $collection = $matches[1];
            $columns = array_map('trim', explode(',', $matches[2]));
            
            // Parse values carefully (handle quoted strings with commas)
            $valuesStr = $matches[3];
            $values = parseValues($valuesStr);
            
            if (count($columns) !== count($values)) {
                error_log("Column/value count mismatch. Columns: " . count($columns) . ", Values: " . count($values));
                error_log("Columns: " . implode(', ', $columns));
                error_log("Values: " . implode(', ', $values));
                return false;
            }
            
            $document = array_combine($columns, $values);
            $document['_id'] = new MongoDB\BSON\ObjectId();
            $document['created_at'] = new MongoDB\BSON\UTCDateTime();
            
            $result = $mongoDatabase->selectCollection($collection)->insertOne($document);
            
            if ($result->getInsertedCount() > 0) {
                return new MongoDBResult([['inserted_id' => (string)$result->getInsertedId()]]);
            }
            return false;
        }
        
        // Parse UPDATE query
        if (preg_match('/^UPDATE\s+(\w+)\s+SET\s+(.+?)\s+WHERE\s+(.+)$/is', trim($sql), $matches)) {
            $collection = $matches[1];
            $setClause = $matches[2];
            $whereClause = $matches[3];
            
            $filter = parseWhereClause($whereClause);
            $update = parseSetClause($setClause);
            
            $result = $mongoDatabase->selectCollection($collection)->updateMany($filter, ['$set' => $update]);
            
            return new MongoDBResult([['modified_count' => $result->getModifiedCount()]]);
        }
        
        // Parse DELETE query
        if (preg_match('/^DELETE\s+FROM\s+(\w+)\s+WHERE\s+(.+)$/i', trim($sql), $matches)) {
            $collection = $matches[1];
            $whereClause = $matches[2];
            
            $filter = parseWhereClause($whereClause);
            $result = $mongoDatabase->selectCollection($collection)->deleteMany($filter);
            
            return new MongoDBResult([['deleted_count' => $result->getDeletedCount()]]);
        }
        
        error_log("Unsupported SQL query: $sql");
        return false;
        
    } catch (Exception $e) {
        error_log("MongoDB Query Error: " . $e->getMessage() . " | SQL: $sql");
        return false;
    }
}

/**
 * Parse VALUES string handling quoted strings with commas
 */
function parseValues($valuesStr) {
    $values = [];
    $current = '';
    $inQuote = false;
    $quoteChar = '';
    
    for ($i = 0; $i < strlen($valuesStr); $i++) {
        $char = $valuesStr[$i];
        
        if (!$inQuote && ($char === "'" || $char === '"')) {
            $inQuote = true;
            $quoteChar = $char;
        } elseif ($inQuote && $char === $quoteChar) {
            // Check for escaped quote
            if ($i + 1 < strlen($valuesStr) && $valuesStr[$i + 1] === $quoteChar) {
                $current .= $char;
                $i++; // Skip next char
            } else {
                $inQuote = false;
                $quoteChar = '';
            }
        } elseif (!$inQuote && $char === ',') {
            $values[] = trim($current, "' \"");
            $current = '';
            continue;
        } else {
            $current .= $char;
        }
    }
    
    // Add last value
    if ($current !== '' || count($values) > 0) {
        $values[] = trim($current, "' \"");
    }
    
    return $values;
}

/**
 * Parse WHERE clause into MongoDB filter
 */
function parseWhereClause($where) {
    if (empty($where)) {
        return [];
    }
    
    $filter = [];
    
    // Split by AND
    $conditions = preg_split('/\s+AND\s+/i', $where);
    
    foreach ($conditions as $condition) {
        $condition = trim($condition);
        
        // Match: field = 'value' or field = "value" or field = value
        if (preg_match("/(\w+)\s*=\s*'([^']*)'/", $condition, $m)) {
            $filter[$m[1]] = $m[2];
        } elseif (preg_match('/(\w+)\s*=\s*"([^"]*)"/', $condition, $m)) {
            $filter[$m[1]] = $m[2];
        } elseif (preg_match('/(\w+)\s*=\s*(\d+)/', $condition, $m)) {
            $filter[$m[1]] = $m[2];
        } elseif (preg_match('/(\w+)\s*=\s*(\S+)/', $condition, $m)) {
            $filter[$m[1]] = trim($m[2], "'\"");
        }
    }
    
    return $filter;
}

/**
 * Parse SET clause into update document
 */
function parseSetClause($setClause) {
    $update = [];
    
    // Split by comma, but be careful with quoted values
    $pairs = [];
    $current = '';
    $inQuote = false;
    
    for ($i = 0; $i < strlen($setClause); $i++) {
        $char = $setClause[$i];
        
        if ($char === "'" && !$inQuote) {
            $inQuote = true;
            $current .= $char;
        } elseif ($char === "'" && $inQuote) {
            $inQuote = false;
            $current .= $char;
        } elseif ($char === ',' && !$inQuote) {
            $pairs[] = trim($current);
            $current = '';
        } else {
            $current .= $char;
        }
    }
    if ($current) {
        $pairs[] = trim($current);
    }
    
    foreach ($pairs as $pair) {
        if (preg_match("/(\w+)\s*=\s*'([^']*)'/", $pair, $m)) {
            $update[$m[1]] = $m[2];
        } elseif (preg_match('/(\w+)\s*=\s*"([^"]*)"/', $pair, $m)) {
            $update[$m[1]] = $m[2];
        } elseif (preg_match('/(\w+)\s*=\s*(\d+)/', $pair, $m)) {
            $update[$m[1]] = $m[2];
        } elseif (preg_match('/(\w+)\s*=\s*(\S+)/', $pair, $m)) {
            $update[$m[1]] = trim($m[2], "'\"");
        }
    }
    
    return $update;
}

/**
 * Fetch associative array from result
 */
function db_fetch_assoc($result) {
    if (!$result || !($result instanceof MongoDBResult)) {
        return false;
    }
    return $result->fetch();
}

/**
 * Get number of rows in result
 */
function db_num_rows($result) {
    if (!$result || !($result instanceof MongoDBResult)) {
        return 0;
    }
    return $result->count();
}

/**
 * Escape string (not needed for MongoDB, but for compatibility)
 */
function db_escape_string($string) {
    return $string;
}

/**
 * Get last error
 */
function db_error() {
    return "Check server logs for MongoDB errors";
}

/**
 * MongoDB Result wrapper class
 */
class MongoDBResult {
    private $data = [];
    private $position = 0;
    
    public function __construct($data) {
        $this->data = [];
        foreach ($data as $doc) {
            if ($doc instanceof MongoDB\Model\BSONDocument) {
                $this->data[] = (array) $doc->getArrayCopy();
            } elseif (is_array($doc)) {
                $this->data[] = $doc;
            } else {
                $this->data[] = (array) $doc;
            }
        }
        $this->position = 0;
    }
    
    public function fetch() {
        if ($this->position >= count($this->data)) {
            return false;
        }
        
        $row = $this->data[$this->position];
        $this->position++;
        
        // Convert _id to id for compatibility
        if (isset($row['_id'])) {
            $row['id'] = (string) $row['_id'];
        }
        
        return $row;
    }
    
    public function count() {
        return count($this->data);
    }
    
    public function reset() {
        $this->position = 0;
    }
}

// Initialize admin user if not exists
function initializeAdminUser() {
    global $mongoDatabase;
    
    if (!$mongoDatabase) {
        return;
    }
    
    try {
        $adminCollection = $mongoDatabase->selectCollection('admin');
        $existingAdmin = $adminCollection->findOne(['email' => 'admin@digitalwebplus.com']);
        
        if (!$existingAdmin) {
            $adminCollection->insertOne([
                'email' => 'admin@digitalwebplus.com',
                'password' => 'admin123',
                'name' => 'System Administrator',
                'role' => 'admin',
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);
            error_log("Admin user created successfully");
        }
        
        // Initialize settings
        $settingsCollection = $mongoDatabase->selectCollection('settings');
        $existingSettings = $settingsCollection->findOne([]);
        
        if (!$existingSettings) {
            $settingsCollection->insertOne([
                'currency' => '$',
                'bname' => 'Logistics Management System',
                'logo' => 'logo.png',
                'email' => 'admin@digitalwebplus.com',
                'phone' => '+1234567890',
                'baddress' => '123 Business Street',
                'title' => 'Logistics Management',
                'branch' => 'Main Branch',
                'sname' => 'owner-avatar-logistics.onrender.com',
                'apipu' => '',
                'apipr' => ''
            ]);
            error_log("Settings initialized successfully");
        }
    } catch (Exception $e) {
        error_log("Error initializing admin/settings: " . $e->getMessage());
    }
}

// Call initialization
initializeAdminUser();
?>
