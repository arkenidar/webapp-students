<?php
require_once "rb.php";

// Check if the script is running in a web server environment
// if not from CLI
if (php_sapi_name() !== 'cli') { // from web server
    // Set the path to the SQLite database file
    $databasePath = $_SERVER['DOCUMENT_ROOT'] . '/../database/database.db';
} else if (php_sapi_name() === 'cli') { // from CLI
    // Set the path to the SQLite database file
    $databasePath = __DIR__ . '/../database/database.db';
}

// Check if the database file exists
if (!file_exists($databasePath)) {
    die("Database file not found. Please ensure the database is set up correctly.\n($databasePath)\n");
}
// Set up the connection string
$connectionString = 'sqlite:' . $databasePath;

// Connect to SQLite database
R::setup($connectionString);

// Check if the connection is successful
if (!R::testConnection()) {
    die("Failed to connect to the database.\n");
}

// Set up RedBeanPHP to freeze schema changes in production
R::freeze(false);
