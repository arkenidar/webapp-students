<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Manage Teachers</h1>

    <!-- List Teachers -->
    <h2>All Teachers</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require '../database/init.php';

            // Fetch all teachers
            $teachers = R::findAll('teacher');
            foreach ($teachers as $teacher) {
                echo "<tr>";
                echo "<td>" . $teacher->id . "</td>";
                echo "<td>" . htmlspecialchars($teacher->name) . "</td>";
                echo "<td>" . htmlspecialchars($teacher->email) . "</td>";
                echo "<td>";
                echo "<form action='' method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='teacher_id' value='" . $teacher->id . "'>";
                echo "<button type='submit' name='action' value='edit'>Edit</button>";
                echo "</form>";
                echo "<form action='' method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='teacher_id' value='" . $teacher->id . "'>";
                echo "<button type='submit' name='action' value='delete'>Delete</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Add Teacher -->
    <h2>Add a New Teacher</h2>
    <form action="" method="POST">
        <label for="teacher_name">Teacher Name:</label>
        <input type="text" id="teacher_name" name="teacher_name" required><br><br>

        <label for="teacher_email">Teacher Email:</label>
        <input type="email" id="teacher_email" name="teacher_email" required><br><br>

        <button type="submit" name="action" value="add">Add Teacher</button>
    </form>

    <?php
    // Handle adding a new teacher
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
        $teacherName = $_POST['teacher_name'];
        $teacherEmail = $_POST['teacher_email'];

        // Check if email already exists
        $existingTeacher = R::findOne('teacher', 'email = ?', [$teacherEmail]);
        if ($existingTeacher) {
            echo "<p class='error-message'>A teacher with this email already exists. No changes were made.</p>";
        } else {
            $teacher = R::dispense('teacher');
            $teacher->name = $teacherName;
            $teacher->email = $teacherEmail;
            R::store($teacher);

            // Redirect to reload the page after successful addition
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    // Edit Teacher
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
        $teacherId = $_POST['teacher_id'];
        $teacher = R::load('teacher', $teacherId);

        if ($teacher->id) {
            echo "<h2>Edit Teacher</h2>";
            echo "<form action='' method='POST'>";
            echo "<input type='hidden' name='teacher_id' value='" . $teacher->id . "'>";
            echo "<label for='teacher_name'>Name:</label>";
            echo "<input type='text' id='teacher_name' name='teacher_name' value='" . htmlspecialchars($teacher->name) . "' required><br><br>";
            echo "<label for='teacher_email'>Email:</label>";
            echo "<input type='email' id='teacher_email' name='teacher_email' value='" . htmlspecialchars($teacher->email) . "' required><br><br>";
            echo "<button type='submit' name='action' value='update'>Update</button>";
            echo "</form>";
        } else {
            echo "<p>Teacher not found.</p>";
        }
    }

    // Update Teacher
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
        $teacherId = $_POST['teacher_id'];
        $teacherName = $_POST['teacher_name'];
        $teacherEmail = $_POST['teacher_email'];

        $teacher = R::load('teacher', $teacherId);
        if ($teacher->id) {
            $teacher->name = $teacherName;
            $teacher->email = $teacherEmail;
            R::store($teacher);
            echo "<p>Teacher updated successfully.</p>";
        } else {
            echo "<p>Teacher not found.</p>";
        }
    }

    // Delete Teacher
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
        $teacherId = $_POST['teacher_id'];
        $teacher = R::load('teacher', $teacherId);

        if ($teacher->id) {
            R::trash($teacher);
            echo "<p>Teacher deleted successfully.</p>";
        } else {
            echo "<p>Teacher not found.</p>";
        }
    }

    // Redirect after processing the form to refresh the page
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !in_array($_POST['action'], ['edit', 'add'])) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    ?>
</body>

</html>