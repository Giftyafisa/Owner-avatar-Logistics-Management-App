<?php

// PostgreSQL connection parameters
$host = 'dpg-d4h6rvumcj7s73brb53g-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'dbname_odlo';
$user = 'dbuser';
$password = 'IRPMRemSEj0V0Kj3lv2XmFEuN5gIY3dR';

// Connect to PostgreSQL
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$conn = pg_connect($conn_string);

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Track ID and new location
$track_id = 'JJHDJ1HG89';
$new_location = 'Frankfurt 🇩🇪';

// Check if track exists
$query = "SELECT id, location FROM track WHERE pid = $1";
$result = pg_query_params($conn, $query, array($track_id));

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

if (pg_num_rows($result) == 0) {
    echo "Track ID $track_id not found in database.\n";
    pg_close($conn);
    exit;
}

$row = pg_fetch_assoc($result);
$current_location = $row['location'];
$id = $row['id'];

echo "Current location for Track ID $track_id: $current_location\n";

// Update the location
$update_query = "UPDATE track SET location = $1 WHERE id = $2";
$update_result = pg_query_params($conn, $update_query, array($new_location, $id));

if (!$update_result) {
    die("Update failed: " . pg_last_error($conn));
}

echo "Location updated successfully to: $new_location\n";

// Verify the update
$verify_query = "SELECT location FROM track WHERE id = $1";
$verify_result = pg_query_params($conn, $verify_query, array($id));
$verify_row = pg_fetch_assoc($verify_result);
echo "Verified new location: " . $verify_row['location'] . "\n";

pg_close($conn);
?>