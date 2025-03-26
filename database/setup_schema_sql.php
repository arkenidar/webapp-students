<?php
require_once 'init.php';

// Create the student table
R::exec('CREATE TABLE IF NOT EXISTS student (
id INTEGER PRIMARY KEY AUTOINCREMENT,
name TEXT NOT NULL,
email TEXT UNIQUE NOT NULL
)');

// Create the course table
R::exec('CREATE TABLE IF NOT EXISTS course (
id INTEGER PRIMARY KEY AUTOINCREMENT,
name TEXT NOT NULL
)');

// Create the subscription table
R::exec('CREATE TABLE IF NOT EXISTS subscription (
id INTEGER PRIMARY KEY AUTOINCREMENT,
student_id INTEGER NOT NULL,
course_id INTEGER NOT NULL,
FOREIGN KEY(student_id) REFERENCES student(id),
FOREIGN KEY(course_id) REFERENCES course(id)
)');

// Create the topic table
R::exec('CREATE TABLE IF NOT EXISTS topic (
id INTEGER PRIMARY KEY AUTOINCREMENT,
title TEXT NOT NULL,
course_id INTEGER NOT NULL,
teacher_id INTEGER,
FOREIGN KEY(course_id) REFERENCES course(id),
FOREIGN KEY(teacher_id) REFERENCES teacher(id)
)');

// Create the teacher table
R::exec('CREATE TABLE IF NOT EXISTS teacher (
id INTEGER PRIMARY KEY AUTOINCREMENT,
name TEXT NOT NULL,
email TEXT UNIQUE NOT NULL
)');

// Create the student_topic table
R::exec('CREATE TABLE IF NOT EXISTS student_topic (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    topic_id INTEGER NOT NULL,
    FOREIGN KEY(student_id) REFERENCES student(id),
    FOREIGN KEY(topic_id) REFERENCES topic(id)
)');
