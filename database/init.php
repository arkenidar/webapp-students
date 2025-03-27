<?php

/**
 * Copyright 2025 Dario Cangialosi
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
