<?php

/* Database credentials from environment variables or defaults */
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'trusqerp_medidb';
$db_pass = getenv('DB_PASS') ?: 'Gravity90$';
$db_name = getenv('DB_NAME') ?: 'trusqerp_medidb';
$db_type = getenv('DB_TYPE') ?: 'mysql'; // 'mysql' or 'pgsql'

if ($db_type === 'pgsql') {
    // PostgreSQL connection
    $conn_string = "host=$db_host dbname=$db_name user=$db_user password=$db_pass";
    $link = pg_connect($conn_string);
    
    if(!$link) {
        die("ERROR: Could not connect to PostgreSQL. " . pg_last_error());
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
