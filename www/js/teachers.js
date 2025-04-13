/**
 * teachers.js - Module for teacher management functionality
 * This module implements the methods called from the teachers tab in the UI
 */

// Map store actions to global methods for the Vue app
function setupTeachersModule(app) {
    // We need to get the store from Pinia after the app instance is created
    app.config.globalProperties.teacherState = null; // Initialize state placeholder

    // Register a component to make sure Pinia store is accessible
    app.mixin({
        created() {
            // Only initialize once
            if (!app.config.globalProperties.teacherState) {
                const teacherStore = useTeacherStore();
                app.config.globalProperties.teacherState = teacherStore;

                // Add Teacher
                app.config.globalProperties.addTeacher = async function () {
                    const newTeacher = { ...teacherStore.newTeacher };

                    // Basic validation
                    if (!newTeacher.name || !newTeacher.email) {
                        teacherStore.setMessage('error', 'Please fill in all fields');
                        return;
                    }

                    await teacherStore.createTeacher(newTeacher);
                };

                // Edit Teacher
                app.config.globalProperties.editTeacher = function (teacher) {
                    teacherStore.enterEditMode(teacher);
                };

                // Update Teacher
                app.config.globalProperties.updateTeacher = async function () {
                    const teacher = { ...teacherStore.editingTeacher };

                    // Basic validation
                    if (!teacher.name || !teacher.email) {
                        teacherStore.setMessage('error', 'Please fill in all fields');
                        return;
                    }

                    await teacherStore.updateTeacher(teacher.id, teacher);
                };

                // Cancel Edit
                app.config.globalProperties.cancelEditTeacher = function () {
                    teacherStore.exitEditMode();
                };

                // Delete Teacher
                app.config.globalProperties.deleteTeacher = async function (id) {
                    if (confirm('Are you sure you want to delete this teacher?')) {
                        await teacherStore.deleteTeacher(id);
                    }
                };

                // Load initial data
                app.config.globalProperties.loadTeachers = function () {
                    console.log("Loading teachers data");
                    teacherStore.fetchTeachers();
                };
            }
        }
    });
}