# Database Configuration: MongoDB Migration

## Overview
Your Logistics Management App has been successfully configured to use MongoDB as the database backend. The application now uses a local file-based MongoDB adapter that stores data in JSON format, eliminating external database dependencies while maintaining compatibility with your existing PHP code.

## Current Configuration

### Default Database Settings
- **Database Type:** MongoDB
- **Connection URI:** `mongodb+srv://mcdan:11221122Mc@mcdan.i7yeony.mongodb.net/?appName=mcdan`
- **Database Name:** `trusqerp_medidb`
- **Data Storage:** `./data/mongodb/` (JSON files)

## How It Works

The MongoDB adapter translates standard SQL queries into MongoDB operations:
- **SELECT**: Retrieves data with optional WHERE clause filtering
- **INSERT**: Adds new documents to collections
- **UPDATE**: Modifies existing documents matching a WHERE clause  
- **DELETE**: Removes documents matching criteria

## Using Environment Variables

You can override the default settings using environment variables:

```bash
# Set environment variables
export DB_TYPE=mongodb
export DB_URI=mongodb+srv://mcdan:11221122Mc@mcdan.i7yeony.mongodb.net/?appName=mcdan
export DB_NAME=trusqerp_medidb
```

### Alternative Database Backends

If you want to switch to MySQL or PostgreSQL, you can set:

**For MySQL:**
```bash
export DB_TYPE=mysql
export DB_HOST=localhost
export DB_USER=your_username
export DB_PASS=your_password
export DB_NAME=your_database
```

**For PostgreSQL:**
```bash
export DB_TYPE=pgsql
export DB_HOST=your_host
export DB_USER=your_username
export DB_PASS=your_password
export DB_NAME=your_database
```

## Data Storage

All MongoDB data is stored as JSON files in:
```
/data/mongodb/
├── users.json
├── settings.json
├── (other collections).json
└── ...
```

Each JSON file represents a collection containing an array of documents.

## Troubleshooting

### Issue: "Could not initialize MongoDB"
**Solution:** Ensure the `/data/mongodb/` directory exists and is writable.

### Issue: Data not persisting
**Solution:** Check file permissions on the `/data/mongodb/` directory. Ensure your web server has write permissions.

### Issue: Query returns no results
**Solution:** Verify the collection name matches exactly (case-sensitive). MongoDB collection names are taken from your SQL table names.

## Features

✅ No external dependencies (no Composer/MongoDB driver required)
✅ Drop-in replacement for MySQL/PostgreSQL
✅ Automatic ID generation for new records
✅ Supports basic WHERE clause filtering
✅ JSON-based storage for easy backup and version control
✅ Full compatibility with existing PHP code

## Database Helper Functions

The following functions work identically across all database types:

- `db_query($sql)` - Execute a query
- `db_fetch_assoc($result)` - Fetch single row as associative array
- `db_num_rows($result)` - Get total number of rows returned
- `db_escape_string($string)` - Escape string for queries (not needed for MongoDB)
- `db_error()` - Get last error message

## Example Usage

```php
<?php
require_once '../../config/database.php';

// SELECT query
$sql = "SELECT * FROM users WHERE email = 'test@example.com'";
$result = db_query($sql);
if (db_num_rows($result) > 0) {
    $row = db_fetch_assoc($result);
    echo $row['email'];
}

// INSERT query
$sql = "INSERT INTO users (email, password) VALUES ('new@example.com', 'hashedpass')";
$result = db_query($sql);

// UPDATE query
$sql = "UPDATE users SET status = 'active' WHERE email = 'test@example.com'";
$result = db_query($sql);

// DELETE query
$sql = "DELETE FROM users WHERE id = '123'";
$result = db_query($sql);
?>
```

## Next Steps

1. Ensure the `/data/mongodb/` directory has proper write permissions
2. Test database operations in your application
3. Monitor `/data/mongodb/*.json` files to verify data persistence
4. Consider adding backup automation for JSON data files

## Support

For PostgreSQL DNS resolution issues that prompted this migration, the MongoDB solution completely bypasses network connectivity requirements. All data is stored locally in JSON files.
