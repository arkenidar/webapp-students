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
    <title>Subscribe to Course</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <?php
    require_once '../database/init.php';
    ?>

    <h2>Add a New Student</h2>
    <form action="" method="POST">
        <label for="student_name">Student Name:</label>
        <input type="text" id="student_name" name="student_name" required><br><br>

        <label for="student_email">Student Email:</label>
        <input type="email" id="student_email" name="student_email" required><br><br>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_student') {
            $studentName = $_POST['student_name'];
            $studentEmail = $_POST['student_email'];

            $existingStudent = R::findOne('student', 'email = ?', [$studentEmail]);
            if ($existingStudent) {
                echo "<p class='error-message'>A student with this email already exists. No changes were made.</p>";
            } else {
                $student = R::dispense('student');
                $student->name = $studentName;
                $student->email = $studentEmail;
                try {
                    R::store($student);
                    $successMessage = "Student added successfully.";
                } catch (Exception $e) {
                    $errorMessage = "An error occurred while adding the student: " . htmlspecialchars($e->getMessage());
                }

                if (isset($errorMessage)) {
                    echo "<p class='error-message'>" . htmlspecialchars($errorMessage) . "</p>";
                } else if (isset($successMessage)) {
                    echo "<p class='success-message'>" . htmlspecialchars($successMessage) . "</p>";
                }

                echo "<a href=''> refresh page </a>";
            }
        }
        ?>

        <button type="submit" name="action" value="add_student">Add Student</button>
    </form>

    <h2>Edit or Delete Students</h2>
    <ul>
        <?php
        // Fetch all students
        $students = R::findAll('student');
        foreach ($students as $student) {
            echo "<li>" . htmlspecialchars($student->name) . " (" . htmlspecialchars($student->email) . ")";
            echo "<form action='' method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='student_id' value='" . $student->id . "'>";
            echo "<button type='submit' name='action' value='edit_student'>Edit</button>";
            echo "</form>";
            echo "<form action='' method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='student_id' value='" . $student->id . "'>";
            echo "<button type='submit' name='action' value='delete_student'>Delete</button>";
            echo "</form>";
            echo "</li>";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit_student') {
            $studentId = $_POST['student_id'];
            $student = R::load('student', $studentId);

            if ($student->id) {
                echo "<h3>Edit Student</h3>";
                echo "<form action='' method='POST'>";
                echo "<input type='hidden' name='student_id' value='" . $student->id . "'>";
                echo "<label for='student_name'>Name:</label>";
                echo "<input type='text' id='student_name' name='student_name' value='" . htmlspecialchars($student->name) . "' required><br><br>";
                echo "<label for='student_email'>Email:</label>";
                echo "<input type='email' id='student_email' name='student_email' value='" . htmlspecialchars($student->email) . "' required><br><br>";
                echo "<button type='submit' name='action' value='update_student'>Update</button>";
                echo "</form>";
            } else {
                echo "<p>Student not found.</p>";
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_student') {
            $studentId = $_POST['student_id'];
            $studentName = $_POST['student_name'];
            $studentEmail = $_POST['student_email'];

            $student = R::load('student', $studentId);

            if ($student->id) {
                $student->name = $studentName;
                $student->email = $studentEmail;
                R::store($student);

                //header('Location: ' . $_SERVER['PHP_SELF']);
                //exit;
                echo "<p class='success-message'>Student updated successfully.</p>";
                echo "<a href=''> refresh page </a>";
            } else {
                echo "<p>Invalid student ID.</p>";
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete_student') {
            $studentId = $_POST['student_id'];
            $student = R::load('student', $studentId);

            if ($student->id) {
                R::trash($student);

                //header('Location: ' . $_SERVER['PHP_SELF']);
                //exit;
                echo "<p class='success-message'>Student deleted successfully.</p>";
                echo "<a href=''> refresh page </a>";
            } else {
                echo "<p>Student not found.</p>";
            }
        }
        ?>
    </ul>

    <h2>Subscribe a Student to a Topic</h2>
    <form action="" method="POST">
        <label for="student_id">Student:</label>
        <select id="student_id" name="student_id" required>
            <?php
            $students = R::findAll('student');
            foreach ($students as $student) {
                echo "<option value='" . $student->id . "'>" . htmlspecialchars($student->name) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="topic_id">Topic:</label>
        <select id="topic_id" name="topic_id" required>
            <?php
            $topics = R::findAll('topic');
            foreach ($topics as $topic) {
                echo "<option value='" . $topic->id . "'>" . htmlspecialchars($topic->title) . "</option>";
            }
            ?>
        </select><br><br>

        <button type="submit" name="action" value="subscribe_student_to_topic">Subscribe</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'subscribe_student_to_topic') {
        $studentId = $_POST['student_id'];
        $topicId = $_POST['topic_id'];

        $student = R::load('student', $studentId);
        $topic = R::load('topic', $topicId);

        if (!$student->id) {
            echo "<p class='error-message'>Invalid student selected. Please try again.</p>";
            return;
        }

        if (!$topic->id) {
            echo "<p class='error-message'>Invalid topic selected. Please try again.</p>";
            return;
        }

        $existingSubscription = R::findOne('student_topic', 'student_id = ? AND topic_id = ?', [$studentId, $topicId]);

        if ($existingSubscription) {
            echo "<p class='error-message'>The student is already subscribed to this topic.</p>";
        } else {

            // Link the student to the topic

            // This is the many-to-many relationship
            $student->sharedTopicList[] = $topic;

            try {
                // Store the student (and the topic will be stored automatically)
                R::store($student);
                echo "<p class='success-message'>Student successfully subscribed to the topic.</p>";
            } catch (Exception $e) {
                echo "<p class='error-message'>An error occurred while subscribing the student: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }

    if (isset($errorMessage)) {
        echo "<p class='error-message'>" . htmlspecialchars($errorMessage) . "</p>";
    }

    if (isset($successMessage)) {
        echo "<p class='success-message'>" . htmlspecialchars($successMessage) . "</p>";
    }
    ?>

    <h2>Manage Students of a Topic</h2>
    <form action="" method="POST">
        <label for="topic_id">Topic:</label>
        <select id="topic_id" name="topic_id" required>
            <?php
            $topics = R::findAll('topic');
            foreach ($topics as $topic) {
                echo "<option value='" . $topic->id . "'>" . htmlspecialchars($topic->title) . "</option>";
            }
            ?>
        </select><br><br>

        <button type="submit" name="action" value="view_students_of_topic">View Students</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'view_students_of_topic') {
        $topicId = $_POST['topic_id'];
        $topic = R::load('topic', $topicId);

        if (!$topic->id) {
            echo "<p class='error-message'>Invalid topic selected. Please try again.</p>";
            return;
        }

        echo "<h3>Students Subscribed to Topic: " . htmlspecialchars($topic->title) . "</h3>";

        $students = $topic->sharedStudentList;
        if (empty($students)) {
            echo "<p>No students are subscribed to this topic.</p>";
        } else {
            echo "<ul>";
            foreach ($students as $student) {
                echo "<li>" . htmlspecialchars($student->name) . " (" . htmlspecialchars($student->email) . ")</li>";
            }
            echo "</ul>";
        }
    }
    ?>
</body>

</html>