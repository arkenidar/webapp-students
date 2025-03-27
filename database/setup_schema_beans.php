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

require 'init.php';

// Define foreign key constraints explicitly
R::exec('PRAGMA foreign_keys = ON');

// Create a student table
$student = R::dispense('student');
$student->name = 'Sample Student';
R::store($student);

// Create a teacher table
$teacher = R::dispense('teacher');
$teacher->name = 'Sample Teacher';
R::store($teacher);

// Create a course table
$course = R::dispense('course');
$course->title = 'Sample Course';
R::store($course);

// Create a topic table
$topic = R::dispense('topic');
$topic->title = 'Sample Topic';
$topic->course = $course;
R::store($topic);

// Create a subscription table
$subscription = R::dispense('subscription');
$subscription->student = $student;
$subscription->course = $course;
R::store($subscription);

// Ensure foreign key relationships are valid
if (!$student->id || !$course->id) {
    echo "Error: Invalid foreign key relationship detected.";
    exit;
}

// Clean up sample data
if (true) {
    R::trash($student);
    R::trash($teacher);
    R::trash($course);
    R::trash($topic);
    R::trash($subscription);
}

echo "Database schema setup completed. Foreign key constraints enabled.\n";
