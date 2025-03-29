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

// Initialize database connection
require_once '../database/init.php';

// Enable CORS for local development if needed
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Parse JSON input data
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE && $method !== 'GET') {
    sendError('Invalid JSON data', 400);
}

// Route the request
try {
    switch ($action) {
        // Teacher endpoints
        case 'getTeachers':
            getTeachers();
            break;
        case 'addTeacher':
            addTeacher($input);
            break;
        case 'updateTeacher':
            updateTeacher($input);
            break;
        case 'deleteTeacher':
            deleteTeacher($input);
            break;

        // Course endpoints
        case 'getCourses':
            getCourses();
            break;
        case 'addCourse':
            addCourse($input);
            break;
        case 'updateCourse':
            updateCourse($input);
            break;
        case 'deleteCourse':
            deleteCourse($input);
            break;

        // Topic endpoints
        case 'getTopics':
            getTopics();
            break;
        case 'addTopic':
            addTopic($input);
            break;
        case 'deleteTopic':
            deleteTopic($input);
            break;
        case 'assignTeacher':
            assignTeacher($input);
            break;

        // Student endpoints
        case 'getStudents':
            getStudents();
            break;
        case 'addStudent':
            addStudent($input);
            break;
        case 'updateStudent':
            updateStudent($input);
            break;
        case 'deleteStudent':
            deleteStudent($input);
            break;
        case 'subscribeStudentToTopic':
            subscribeStudentToTopic($input);
            break;
        case 'getTopicStudents':
            getTopicStudents($_GET);
            break;

        default:
            sendError('Invalid action', 400);
    }
} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}

// ===== Helper Functions =====

function sendResponse($data, $status = 200)
{
    http_response_code($status);
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

function sendError($message, $status = 400)
{
    http_response_code($status);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

// ===== Teacher Functions =====

function getTeachers()
{
    $teachers = R::findAll('teacher');
    $result = [];

    foreach ($teachers as $teacher) {
        $result[] = [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'email' => $teacher->email
        ];
    }

    sendResponse($result);
}

function addTeacher($input)
{
    // Validate input
    if (!isset($input['name']) || !isset($input['email'])) {
        sendError('Name and email are required', 400);
    }

    // Check for duplicate email
    $existingTeacher = R::findOne('teacher', 'email = ?', [$input['email']]);
    if ($existingTeacher) {
        sendError('A teacher with this email already exists', 409);
    }

    // Create new teacher
    $teacher = R::dispense('teacher');
    $teacher->name = $input['name'];
    $teacher->email = $input['email'];

    $id = R::store($teacher);

    sendResponse([
        'id' => $id,
        'name' => $teacher->name,
        'email' => $teacher->email
    ], 201);
}

function updateTeacher($input)
{
    // Validate input
    if (!isset($input['id']) || !isset($input['name']) || !isset($input['email'])) {
        sendError('ID, name, and email are required', 400);
    }

    $teacher = R::load('teacher', $input['id']);

    if (!$teacher->id) {
        sendError('Teacher not found', 404);
    }

    // Check for duplicate email (excluding current teacher)
    $existingTeacher = R::findOne('teacher', 'email = ? AND id <> ?', [$input['email'], $input['id']]);
    if ($existingTeacher) {
        sendError('Another teacher with this email already exists', 409);
    }

    $teacher->name = $input['name'];
    $teacher->email = $input['email'];

    R::store($teacher);

    sendResponse([
        'id' => $teacher->id,
        'name' => $teacher->name,
        'email' => $teacher->email
    ]);
}

function deleteTeacher($input)
{
    // Validate input
    if (!isset($input['id'])) {
        sendError('Teacher ID is required', 400);
    }

    $teacher = R::load('teacher', $input['id']);

    if (!$teacher->id) {
        sendError('Teacher not found', 404);
    }

    // Update any topics where this teacher is assigned to null
    $topics = R::find('topic', 'teacher_id = ?', [$input['id']]);
    foreach ($topics as $topic) {
        $topic->teacher_id = null;
        R::store($topic);
    }

    R::trash($teacher);

    sendResponse(['message' => 'Teacher deleted successfully']);
}

// ===== Course Functions =====

function getCourses()
{
    $courses = R::findAll('course');
    $result = [];

    foreach ($courses as $course) {
        $result[] = [
            'id' => $course->id,
            'name' => $course->name
        ];
    }

    sendResponse($result);
}

function addCourse($input)
{
    // Validate input
    if (!isset($input['name'])) {
        sendError('Course name is required', 400);
    }

    // Check for duplicate name
    $existingCourse = R::findOne('course', 'name = ?', [$input['name']]);
    if ($existingCourse) {
        sendError('A course with this name already exists', 409);
    }

    // Create new course
    $course = R::dispense('course');
    $course->name = $input['name'];

    $id = R::store($course);

    sendResponse([
        'id' => $id,
        'name' => $course->name
    ], 201);
}

function updateCourse($input)
{
    // Validate input
    if (!isset($input['id']) || !isset($input['name'])) {
        sendError('Course ID and name are required', 400);
    }

    $course = R::load('course', $input['id']);

    if (!$course->id) {
        sendError('Course not found', 404);
    }

    // Check for duplicate name (excluding current course)
    $existingCourse = R::findOne('course', 'name = ? AND id <> ?', [$input['name'], $input['id']]);
    if ($existingCourse) {
        sendError('Another course with this name already exists', 409);
    }

    $course->name = $input['name'];

    R::store($course);

    sendResponse([
        'id' => $course->id,
        'name' => $course->name
    ]);
}

function deleteCourse($input)
{
    // Validate input
    if (!isset($input['id'])) {
        sendError('Course ID is required', 400);
    }

    $course = R::load('course', $input['id']);

    if (!$course->id) {
        sendError('Course not found', 404);
    }

    // Get all topics associated with this course
    $topics = R::find('topic', 'course_id = ?', [$course->id]);

    // Delete all topics associated with this course
    foreach ($topics as $topic) {
        R::trash($topic);
    }

    R::trash($course);

    sendResponse(['message' => 'Course and its topics deleted successfully']);
}

// ===== Topic Functions =====

function getTopics()
{
    $topics = R::findAll('topic');
    $result = [];

    foreach ($topics as $topic) {
        $courseId = $topic->course_id;
        $teacherId = $topic->teacher_id;

        $course = $courseId ? R::load('course', $courseId) : null;
        $teacher = $teacherId ? R::load('teacher', $teacherId) : null;

        $result[] = [
            'id' => $topic->id,
            'title' => $topic->title,
            'course_id' => $courseId,
            'course_name' => $course ? $course->name : '',
            'teacher_id' => $teacherId,
            'teacher_name' => $teacher ? $teacher->name : ''
        ];
    }

    sendResponse($result);
}

function addTopic($input)
{
    // Validate input
    if (!isset($input['title']) || !isset($input['course_id'])) {
        sendError('Topic title and course ID are required', 400);
    }

    // Check if course exists
    $course = R::load('course', $input['course_id']);
    if (!$course->id) {
        sendError('Course not found', 404);
    }

    // Create new topic
    $topic = R::dispense('topic');
    $topic->title = $input['title'];
    $topic->course = $course;

    $id = R::store($topic);

    sendResponse([
        'id' => $id,
        'title' => $topic->title,
        'course_id' => $course->id,
        'course_name' => $course->name
    ], 201);
}

function deleteTopic($input)
{
    // Validate input
    if (!isset($input['id'])) {
        sendError('Topic ID is required', 400);
    }

    $topic = R::load('topic', $input['id']);

    if (!$topic->id) {
        sendError('Topic not found', 404);
    }

    R::trash($topic);

    sendResponse(['message' => 'Topic deleted successfully']);
}

function assignTeacher($input)
{
    // Validate input
    if (!isset($input['topic_id']) || !isset($input['teacher_id'])) {
        sendError('Topic ID and teacher ID are required', 400);
    }

    $topic = R::load('topic', $input['topic_id']);

    if (!$topic->id) {
        sendError('Topic not found', 404);
    }

    // Check if teacher exists (only if teacher_id is not null)
    if (!empty($input['teacher_id'])) {
        $teacher = R::load('teacher', $input['teacher_id']);
        if (!$teacher->id) {
            sendError('Teacher not found', 404);
        }
    }

    $topic->teacher_id = $input['teacher_id'] ?: null;
    R::store($topic);

    sendResponse(['message' => 'Teacher assigned to topic successfully']);
}

// ===== Student Functions =====

function getStudents()
{
    $students = R::findAll('student');
    $result = [];

    foreach ($students as $student) {
        $result[] = [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email
        ];
    }

    sendResponse($result);
}

function addStudent($input)
{
    // Validate input
    if (!isset($input['name']) || !isset($input['email'])) {
        sendError('Student name and email are required', 400);
    }

    // Check for duplicate email
    $existingStudent = R::findOne('student', 'email = ?', [$input['email']]);
    if ($existingStudent) {
        sendError('A student with this email already exists', 409);
    }

    // Create new student
    $student = R::dispense('student');
    $student->name = $input['name'];
    $student->email = $input['email'];

    $id = R::store($student);

    sendResponse([
        'id' => $id,
        'name' => $student->name,
        'email' => $student->email
    ], 201);
}

function updateStudent($input)
{
    // Validate input
    if (!isset($input['id']) || !isset($input['name']) || !isset($input['email'])) {
        sendError('Student ID, name, and email are required', 400);
    }

    $student = R::load('student', $input['id']);

    if (!$student->id) {
        sendError('Student not found', 404);
    }

    // Check for duplicate email (excluding current student)
    $existingStudent = R::findOne('student', 'email = ? AND id <> ?', [$input['email'], $input['id']]);
    if ($existingStudent) {
        sendError('Another student with this email already exists', 409);
    }

    $student->name = $input['name'];
    $student->email = $input['email'];

    R::store($student);

    sendResponse([
        'id' => $student->id,
        'name' => $student->name,
        'email' => $student->email
    ]);
}

function deleteStudent($input)
{
    // Validate input
    if (!isset($input['id'])) {
        sendError('Student ID is required', 400);
    }

    $student = R::load('student', $input['id']);

    if (!$student->id) {
        sendError('Student not found', 404);
    }

    R::trash($student);

    sendResponse(['message' => 'Student deleted successfully']);
}

function subscribeStudentToTopic($input)
{
    // Validate input
    if (!isset($input['student_id']) || !isset($input['topic_id'])) {
        sendError('Student ID and topic ID are required', 400);
    }

    $student = R::load('student', $input['student_id']);
    $topic = R::load('topic', $input['topic_id']);

    if (!$student->id) {
        sendError('Student not found', 404);
    }

    if (!$topic->id) {
        sendError('Topic not found', 404);
    }

    // Check if student is already subscribed to this topic
    $existingSubscription = R::findOne('student_topic', 'student_id = ? AND topic_id = ?', [$input['student_id'], $input['topic_id']]);
    if ($existingSubscription) {
        sendError('The student is already subscribed to this topic', 409);
    }

    // Link the student to the topic (many-to-many relationship)
    $student->sharedTopicList[] = $topic;
    R::store($student);

    sendResponse(['message' => 'Student successfully subscribed to the topic']);
}

function getTopicStudents($params)
{
    // Validate input
    if (!isset($params['topic_id'])) {
        sendError('Topic ID is required', 400);
    }

    $topic = R::load('topic', $params['topic_id']);

    if (!$topic->id) {
        sendError('Topic not found', 404);
    }

    $students = $topic->sharedStudentList;
    $result = [];

    foreach ($students as $student) {
        $result[] = [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email
        ];
    }

    sendResponse($result);
}
