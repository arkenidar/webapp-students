<?php
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
