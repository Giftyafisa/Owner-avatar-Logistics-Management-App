<?php

/* Database credentials from environment variables or defaults */
define('DB_SERVER', getenv('DB_HOST') ?: 'localhost');
define('DB_USERNAME', getenv('DB_USER') ?: 'trusqerp_medidb');
define('DB_PASSWORD', getenv('DB_PASS') ?: 'Gravity90$');
define('DB_NAME', getenv('DB_NAME') ?: 'trusqerp_medidb');
 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
