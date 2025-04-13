/**
 * students.js - Module for student management functionality
 * This module implements the methods called from the students tab in the UI
 */

// Map store actions to global methods for the Vue app
function setupStudentsModule(app) {
    // Initialize state placeholder
    app.config.globalProperties.studentState = null;

    // Register a component to make sure Pinia store is accessible
    app.mixin({
        created() {
            // Only initialize once
            if (!app.config.globalProperties.studentState) {
                const studentStore = useStudentStore();
                app.config.globalProperties.studentState = studentStore;

                // Add Student
                app.config.globalProperties.addStudent = async function () {
                    const newStudent = { ...studentStore.newStudent };

                    // Basic validation
                    if (!newStudent.name || !newStudent.email) {
                        studentStore.setMessage('error', 'Please fill in all fields');
                        return;
                    }

                    await studentStore.createStudent(newStudent);
                };

                // Edit Student
                app.config.globalProperties.editStudent = function (student) {
                    studentStore.enterEditMode(student);
                };

                // Update Student
                app.config.globalProperties.updateStudent = async function () {
                    const student = { ...studentStore.editingStudent };

                    // Basic validation
                    if (!student.name || !student.email) {
                        studentStore.setMessage('error', 'Please fill in all fields');
                        return;
                    }

                    await studentStore.updateStudent(student.id, student);
                };

                // Cancel Edit
                app.config.globalProperties.cancelEditStudent = function () {
                    studentStore.exitEditMode();
                };

                // Delete Student
                app.config.globalProperties.deleteStudent = async function (id) {
                    if (confirm('Are you sure you want to delete this student?')) {
                        await studentStore.deleteStudent(id);
                    }
                };

                // Load initial data
                app.config.globalProperties.loadStudents = function () {
                    console.log("Loading students data");
                    studentStore.fetchStudents();
                };
            }
        }
    });
}