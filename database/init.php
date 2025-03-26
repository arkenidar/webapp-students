<?php
require_once "rb.php";

$databasePath = __DIR__ . '/../database/database.db';

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
