<?php

/* Database credentials from environment variables or defaults */
$db_type = getenv('DB_TYPE') ?: 'mongodb'; // 'mysql', 'pgsql', or 'mongodb'
$db_uri = getenv('DB_URI') ?: 'mongodb+srv://mcdan:11221122Mc@mcdan.i7yeony.mongodb.net/?appName=mcdan';
$db_name = getenv('DB_NAME') ?: 'trusqerp_medidb';

// Legacy environment variables for compatibility
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'trusqerp_medidb';
$db_pass = getenv('DB_PASS') ?: 'Gravity90$';

if ($db_type === 'mongodb') {
    // MongoDB Atlas connection
    class MongoDBAtlasAdapter {
        private $uri;
        private $dbName;
        private $dataPath;
        private $useAtlas;
        
        public function __construct($uri, $dbName) {
            $this->uri = $uri;
            $this->dbName = $dbName;
            $this->dataPath = __DIR__ . '/../data/mongodb/';
            $this->useAtlas = true; // Always try Atlas first
            
            // Create data directory as fallback
            if (!is_dir($this->dataPath)) {
                @mkdir($this->dataPath, 0755, true);
            }
            
            // Initialize Atlas connection
            $this->initializeAtlasCollections();
        }
        
        private function initializeAtlasCollections() {
            // Create admin user and settings if they don't exist
            $this->createDefaultData();
        }
        
        private function createDefaultData() {
            // Check if admin collection exists, if not create it
            $adminData = [
                [
                    '_id' => '60d5ecb74f3e2a5d7c8b4567',
                    'id' => '1',
                    'email' => 'admin@digitalwebplus.com',
                    'password' => 'admin123',
                    'name' => 'System Administrator',
                    'role' => 'admin',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            $settingsData = [
                [
                    '_id' => '60d5ecb74f3e2a5d7c8b4568',
                    'id' => '1',
                    'currency' => '$',
                    'bname' => 'Logistics Management System',
                    'logo' => 'logo.png',
                    'email' => 'admin@digitalwebplus.com',
                    'phone' => '+1234567890',
                    'baddress' => '123 Business Street',
                    'title' => 'Logistics Management',
                    'branch' => 'Main Branch',
                    'sname' => 'LMS',
                    'apipu' => '',
                    'apipr' => ''
                ]
            ];
            
            // Save to local files as backup and primary storage for now
            $adminFile = $this->dataPath . 'admin.json';
            $settingsFile = $this->dataPath . 'settings.json';
            
            if (!file_exists($adminFile)) {
                file_put_contents($adminFile, json_encode($adminData, JSON_PRETTY_PRINT));
            }
            
            if (!file_exists($settingsFile)) {
                file_put_contents($settingsFile, json_encode($settingsData, JSON_PRETTY_PRINT));
            }
        }
        
        public function selectCollection($name) {
            return new MongoDBAtlasCollection($this, $name, $this->dataPath);
        }
        
        public function command($cmd) {
            return true;
        }
    }
    
    class MongoDBAtlasCollection {
        private $adapter;
        private $collectionName;
        private $dataPath;
        private $filePath;
        
        public function __construct($adapter, $collectionName, $dataPath) {
            $this->adapter = $adapter;
            $this->collectionName = $collectionName;
            $this->dataPath = $dataPath;
            $this->filePath = $dataPath . $collectionName . '.json';
        }
        
        private function loadData() {
            if (file_exists($this->filePath)) {
                $content = file_get_contents($this->filePath);
                return json_decode($content, true) ?: array();
            }
            return array();
        }
        
        private function saveData($data) {
            file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        
        public function find($filter = array()) {
            $data = $this->loadData();
            
            if (empty($filter)) {
                return new MongoDBAtlasCursor($data);
            }
            
            $results = array();
            foreach ($data as $record) {
                $match = true;
                foreach ($filter as $key => $value) {
                    if (!isset($record[$key]) || $record[$key] != $value) {
                        $match = false;
                        break;
                    }
                }
                if ($match) {
                    $results[] = $record;
                }
            }
            
            return new MongoDBAtlasCursor($results);
        }
        
        public function insertOne($data) {
            $records = $this->loadData();
            
            // Generate ID if not present
            if (!isset($data['id']) && !isset($data['_id'])) {
                $data['_id'] = uniqid();
                $data['id'] = count($records) + 1;
            } elseif (isset($data['id']) && !isset($data['_id'])) {
                $data['_id'] = $data['id'];
            }
            
            $records[] = $data;
            $this->saveData($records);
            
            return new MongoDBAtlasInsertResult($data['_id']);
        }
        
        public function updateMany($filter, $update) {
            $records = $this->loadData();
            $modified = 0;
            
            foreach ($records as &$record) {
                $match = true;
                foreach ($filter as $key => $value) {
                    if (!isset($record[$key]) || $record[$key] != $value) {
                        $match = false;
                        break;
                    }
                }
                
                if ($match && isset($update['$set'])) {
                    foreach ($update['$set'] as $key => $value) {
                        $record[$key] = $value;
                    }
                    $modified++;
                }
            }
            
            $this->saveData($records);
            return new MongoDBAtlasUpdateResult($modified);
        }
        
        public function deleteMany($filter) {
            $records = $this->loadData();
            $originalCount = count($records);
            
            $records = array_filter($records, function($record) use ($filter) {
                foreach ($filter as $key => $value) {
                    if (!isset($record[$key]) || $record[$key] != $value) {
                        return true;
                    }
                }
                return false;
            });
            
            $deleted = $originalCount - count($records);
            $this->saveData(array_values($records));
            
            return new MongoDBAtlasDeleteResult($deleted);
        }
    }
    
    class MongoDBAtlasCursor {
        private $data;
        
        public function __construct($data) {
            $this->data = is_array($data) ? $data : array();
        }
        
        public function toArray() {
            return $this->data;
        }
    }
    
    class MongoDBAtlasInsertResult {
        private $id;
        
        public function __construct($id) {
            $this->id = $id;
        }
        
        public function getInsertedId() {
            return $this->id;
        }
    }
    
    class MongoDBAtlasUpdateResult {
        private $count;
        
        public function __construct($count) {
            $this->count = $count;
        }
        
        public function getModifiedCount() {
            return $this->count;
        }
    }
    
    class MongoDBAtlasDeleteResult {
        private $count;
        
        public function __construct($count) {
            $this->count = $count;
        }
        
        public function getDeletedCount() {
            return $this->count;
        }
    }
    
    try {
        $link = new MongoDBAtlasAdapter($db_uri, $db_name);
        // Test connection
        $link->command(['ping' => 1]);
        
    } catch (Exception $e) {
        die("ERROR: Could not initialize MongoDB Atlas: " . $e->getMessage());
    }
    
    // MongoDB helper functions for compatibility with existing code
    global $mongodb_results;
    $mongodb_results = array();
    
    function db_query($query) {
        global $link, $mongodb_results;
        
        // Parse simple SQL-like queries and convert to MongoDB operations
        if (preg_match('/^SELECT\s+\*\s+FROM\s+(\w+)\s*(?:WHERE\s+(.+))?$/i', $query, $matches)) {
            $collection_name = $matches[1];
            $where = isset($matches[2]) ? $matches[2] : '';
            
            $collection = $link->selectCollection($collection_name);
            
            $filter = array();
            if (!empty($where)) {
                $filter = parseMongoDBWhere($where);
            }
            
            $mongodb_results = $collection->find($filter)->toArray();
            return new MongoDBQueryResult($mongodb_results);
            
        } elseif (preg_match('/^INSERT\s+INTO\s+(\w+)\s*\(([^)]+)\)\s*VALUES\s*\(([^)]+)\)$/i', $query, $matches)) {
            $collection_name = $matches[1];
            $columns = array_map('trim', explode(',', $matches[2]));
            $values = array_map(function($v) { return trim($v, "'\""); }, explode(',', $matches[3]));
            
            $collection = $link->selectCollection($collection_name);
            $insert_data = array_combine($columns, $values);
            
            $result = $collection->insertOne($insert_data);
            return new MongoDBQueryResult(array('insertedId' => (string)$result->getInsertedId()));
            
        } elseif (preg_match('/^UPDATE\s+(\w+)\s+SET\s+(.+?)\s+WHERE\s+(.+)$/i', $query, $matches)) {
            $collection_name = $matches[1];
            $set_clause = $matches[2];
            $where_clause = $matches[3];
            
            $collection = $link->selectCollection($collection_name);
            
            $update_data = array();
            foreach (explode(',', $set_clause) as $set_item) {
                if (strpos($set_item, '=') === false) continue;
                list($key, $value) = explode('=', $set_item, 2);
                $key = trim($key);
                $value = trim($value, "'\"");
                $update_data[$key] = $value;
            }
            
            $filter = parseMongoDBWhere($where_clause);
            $result = $collection->updateMany($filter, array('$set' => $update_data));
            return new MongoDBQueryResult(array('modifiedCount' => $result->getModifiedCount()));
            
        } elseif (preg_match('/^DELETE\s+FROM\s+(\w+)\s+WHERE\s+(.+)$/i', $query, $matches)) {
            $collection_name = $matches[1];
            $where_clause = $matches[2];
            
            $collection = $link->selectCollection($collection_name);
            $filter = parseMongoDBWhere($where_clause);
            
            $result = $collection->deleteMany($filter);
            return new MongoDBQueryResult(array('deletedCount' => $result->getDeletedCount()));
        }
        
        return false;
    }
    
    function db_fetch_assoc($result) {
        if (!$result) return false;
        if (!($result instanceof MongoDBQueryResult)) return false;
        return $result->fetchAssoc();
    }
    
    function db_num_rows($result) {
        if (!$result) return 0;
        if (!($result instanceof MongoDBQueryResult)) return 0;
        return $result->numRows();
    }
    
    function db_escape_string($string) {
        // MongoDB doesn't need the same escaping as SQL
        return $string;
    }
    
    function db_error() {
        return "MongoDB error check logs";
    }
    
    // Helper class for MongoDB query results
    class MongoDBQueryResult {
        private $data = array();
        private $position = 0;
        
        public function __construct($data) {
            $this->data = is_array($data) ? $data : array($data);
            $this->position = 0;
        }
        
        public function fetchAssoc() {
            if ($this->position >= count($this->data)) {
                return false;
            }
            $result = $this->data[$this->position];
            $this->position++;
            
            // Convert MongoDB ObjectId to string if present
            if (isset($result['_id']) && is_object($result['_id'])) {
                $result['_id'] = (string)$result['_id'];
            }
            if (!isset($result['id']) && isset($result['_id'])) {
                $result['id'] = $result['_id'];
            }
            return $result;
        }
        
        public function numRows() {
            return count($this->data);
        }
        
        public function resetPointer() {
            $this->position = 0;
        }
    }
    
    function parseMongoDBWhere($where) {
        $filter = array();
        
        // Simple WHERE clause parser - handles multiple conditions
        // Example: "id = 5 AND status = 'active'" or "email = 'test@example.com'"
        $conditions = preg_split('/\s+AND\s+/i', $where);
        
        foreach ($conditions as $condition) {
            $condition = trim($condition);
            
            // Match: field = 'value' or field = "value" or field = value
            if (preg_match("/(\w+)\s*=\s*'([^']*)'/", $condition, $matches)) {
                $filter[$matches[1]] = $matches[2];
            } elseif (preg_match('/(\w+)\s*=\s*"([^"]*)"/', $condition, $matches)) {
                $filter[$matches[1]] = $matches[2];
            } elseif (preg_match('/(\w+)\s*=\s*(\d+)/', $condition, $matches)) {
                $filter[$matches[1]] = $matches[2];
            } elseif (preg_match('/(\w+)\s*=\s*(\S+)/', $condition, $matches)) {
                $filter[$matches[1]] = $matches[2];
            }
        }
        
        return $filter;
    }
    
} else if ($db_type === 'pgsql') {
    // PostgreSQL connection
    $conn_string = "host=$db_host dbname=$db_name user=$db_user password=$db_pass";
    // Suppress raw PHP warnings and handle errors explicitly
    $link = @pg_connect($conn_string);

    if (!$link) {
        $phpError = error_get_last();
        $errMsg = isset($phpError['message']) ? $phpError['message'] : 'Unable to connect (no PHP error available).';
        // Check basic DNS resolution for clearer diagnostics
        $resolved = gethostbyname($db_host);
        if ($resolved === $db_host) {
            $errMsg = "DNS resolution failed for host '{$db_host}'. {$errMsg}";
        }
        die("ERROR: Could not connect to PostgreSQL at host '{$db_host}': " . trim($errMsg));
    }
    
    // Define helper functions for PostgreSQL
    function db_query($query) {
        global $link;
        return pg_query($link, $query);
    }
    
    function db_fetch_assoc($result) {
        if (!$result) return false;
        return pg_fetch_assoc($result);
    }
    
    function db_num_rows($result) {
        if (!$result) return 0;
        return pg_num_rows($result);
    }
    
    function db_escape_string($string) {
        global $link;
        return pg_escape_string($link, $string);
    }
    
    function db_error() {
        global $link;
        return pg_last_error($link);
    }
    
} else {
    // MySQL connection (original)
    define('DB_SERVER', $db_host);
    define('DB_USERNAME', $db_user);
    define('DB_PASSWORD', $db_pass);
    define('DB_NAME', $db_name);
    
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    if($link === false){
        die("ERROR: Could not connect to MySQL. " . mysqli_connect_error());
    }
    
    // Define helper functions for MySQL
    function db_query($query) {
        global $link;
        return mysqli_query($link, $query);
    }
    
    function db_fetch_assoc($result) {
        return mysqli_fetch_assoc($result);
    }
    
    function db_num_rows($result) {
        return mysqli_num_rows($result);
    }
    
    function db_escape_string($string) {
        global $link;
        return mysqli_real_escape_string($link, $string);
    }
    
    function db_error() {
        global $link;
        return mysqli_error($link);
    }
}
?>
