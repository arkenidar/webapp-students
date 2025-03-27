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
    <title>Manage Topics and Courses</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <?php
    require_once '../database/init.php';
    ?>

    <h2>Add a New Course</h2>
    <form action="" method="POST">
        <label for="course_name">Course Name:</label>
        <input type="text" id="course_name" name="course_name" required><br><br>

        <button type="submit" name="action" value="create_course">Create Course</button>
    </form>

    <?php

    // Handle course creation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create_course') {
        $courseName = $_POST['course_name'];

        // Check for duplicate course
        $existingCourse = R::findOne('course', 'name = ?', [$courseName]);
        if ($existingCourse) {
            echo "<p>Course already exists.</p>";
        } else {
            $course = R::dispense('course');
            $course->name = $courseName;
            R::store($course);

            // Redirect to avoid form resubmission
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    // Display existing courses
    $courses = R::findAll('course');

    ?>


    <h2>Edit or Delete Courses</h2>
    <ul>
        <?php
        // Display existing courses with edit and delete options
        foreach ($courses as $course) {
            echo "<li>" . htmlspecialchars($course->name);
            echo "<form action='' method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='course_id' value='" . $course->id . "'>";
            echo "<button type='submit' name='action' value='edit_course'>Edit</button>";
            echo "</form>";
            echo "<form action='' method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='course_id' value='" . $course->id . "'>";
            echo "<button type='submit' name='action' value='delete_course'>Delete</button>";
            echo "</form>";
            echo "</li>";
        }

        // Handle course editing
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit_course') {
            $courseId = $_POST['course_id'];
            $course = R::load('course', $courseId);

            if ($course->id) {
                echo "<h3>Edit Course</h3>";
                echo "<form action='' method='POST'>";
                echo "<input type='hidden' name='course_id' value='" . $course->id . "'>";
                echo "<label for='course_name'>Course Name:</label>";
                echo "<input type='text' id='course_name' name='course_name' value='" . htmlspecialchars($course->name) . "' required><br><br>";
                echo "<button type='submit' name='action' value='update_course'>Update</button>";
                echo "</form>";
            } else {
                echo "<p>Course not found.</p>";
            }
        }

        // Handle course updating
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_course') {
            $courseId = $_POST['course_id'];
            $courseName = $_POST['course_name'];

            $course = R::load('course', $courseId);

            if ($course->id) {
                $course->name = $courseName;
                R::store($course);

                // Redirect to avoid form resubmission
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "<p>Invalid course ID.</p>";
            }
        }

        // Handle course deletion
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete_course') {
            $courseId = $_POST['course_id'];
            $course = R::load('course', $courseId);

            if ($course->id) {
                R::trash($course);

                // Redirect to avoid form resubmission
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "<p>Course not found.</p>";
            }
        }
        ?>
    </ul>

    <h2>Manage Topics</h2>
    <form action="" method="POST">
        <label for="course_id">Course:</label>
        <select id="course_id" name="course_id" required>
            <?php
            $courses = R::findAll('course');
            foreach ($courses as $course) {
                echo "<option value='" . $course->id . "'>" . htmlspecialchars($course->name) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="topic_title">Topic Title:</label>
        <input type="text" id="topic_title" name="topic_title" required><br><br>

        <button type="submit" name="action" value="create_topic">Add Topic</button>
    </form>

    <h3>Existing Topics</h3>
    <ul>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create_topic') {
            $courseId = $_POST['course_id'];
            $topicTitle = $_POST['topic_title'];

            $course = R::load('course', $courseId);
            if ($course->id) {
                $topic = R::dispense('topic');
                $topic->title = $topicTitle;
                $topic->course = $course;
                R::store($topic);

                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "<p>Invalid course ID.</p>";
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete_topic') {
            $topicId = $_POST['topic_id'];
            $topic = R::load('topic', $topicId);

            if ($topic->id) {
                R::trash($topic);

                // Removed the header redirect to avoid refreshing the page
                echo "<p>Topic successfully deleted.</p>";
            } else {
                echo "<p>Topic not found.</p>";
            }
        }

        $topics = R::findAll('topic');
        foreach ($topics as $topic) {
            echo "<li>" . htmlspecialchars($topic->title) . " (Course: " . htmlspecialchars($topic->course->name) . ")";
            echo "<form action='' method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='topic_id' value='" . $topic->id . "'>";
            echo "<button type='submit' name='action' value='delete_topic'>Delete</button>";
            echo "</form>";
            echo "</li>";
        }
        ?>
    </ul>

    <h2>Topics Overview</h2>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'assign_teacher') {
        $topicId = $_POST['topic_id'];
        $teacherId = $_POST['teacher_id'];

        $topic = R::load('topic', $topicId);
        $teacher = R::load('teacher', $teacherId);

        if ($topic->id && $teacher->id) {
            $topic->teacher_id = $teacherId;
            R::store($topic);

            // Redirect to refresh the page and update the topics overview
            //header('Location: ' . $_SERVER['PHP_SELF']);
            //exit;

            echo "<p>Teacher successfully assigned to topic.</p>";
            echo "<a href=''> refresh page </a>";
        } else {
            echo "<p>Invalid topic or teacher ID.</p>";
        }
    }
    ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Topic Title</th>
                <th>Course</th>
                <th>Teacher</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $topics = R::findAll('topic');
            foreach ($topics as $topic) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($topic->title) . "</td>";
                echo "<td>" . htmlspecialchars($topic->course->name) . "</td>";
                echo "<td>" . ($topic->teacher_id ? htmlspecialchars(R::load('teacher', $topic->teacher_id)->name) : 'Unassigned') . "</td>";
                echo "<td>";
                echo "<form action='' method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='topic_id' value='" . $topic->id . "'>";
                echo "<button type='submit' name='action' value='delete_topic'>Delete</button>";
                echo "</form>";
                echo "<form action='' method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='topic_id' value='" . $topic->id . "'>";
                echo "<select name='teacher_id'>";
                $teachers = R::findAll('teacher');
                foreach ($teachers as $teacher) {
                    $selected = $topic->teacher_id == $teacher->id ? 'selected' : '';
                    echo "<option value='" . $teacher->id . "' $selected>" . htmlspecialchars($teacher->name) . "</option>";
                }
                echo "</select>";
                echo "<button type='submit' name='action' value='assign_teacher'>Assign</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>

</html>