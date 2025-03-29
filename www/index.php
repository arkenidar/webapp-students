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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .spa-link {
            display: block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .spa-link:hover {
            background-color: #2980b9;
        }
    </style>
</head>

<body>
    <h1>Management Dashboard</h1>
    <h2>Traditional PHP Pages</h2>
    <ul>
        <li><a href="manage_teachers.php">Manage Teachers</a></li>
        <li><a href="manage_courses.php">Manage Courses</a></li>
        <li><a href="manage_students.php">Manage Students</a></li>
    </ul>

    <a href="index.html" class="spa-link">Use Vue.js SPA Version</a>
</body>

</html>