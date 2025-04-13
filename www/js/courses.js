/**
 * courses.js - Module for course and topic management functionality
 * This module implements the methods called from the courses tab in the UI
 */

// Map store actions to global methods for the Vue app
function setupCoursesModule(app) {
    // Initialize state placeholder
    app.config.globalProperties.courseState = null;

    // Register a component to make sure Pinia store is accessible
    app.mixin({
        created() {
            // Only initialize once
            if (!app.config.globalProperties.courseState) {
                const courseStore = useCourseStore();
                app.config.globalProperties.courseState = courseStore;

                // Add Course
                app.config.globalProperties.addCourse = async function () {
                    const newCourse = { ...courseStore.newCourse };

                    // Basic validation
                    if (!newCourse.name) {
                        courseStore.setMessage('error', 'Please enter a course name');
                        return;
                    }

                    await courseStore.createCourse(newCourse);
                };

                // Edit Course
                app.config.globalProperties.editCourse = function (course) {
                    courseStore.enterEditMode(course);
                };

                // Update Course
                app.config.globalProperties.updateCourse = async function () {
                    const course = { ...courseStore.editingCourse };

                    // Basic validation
                    if (!course.name) {
                        courseStore.setMessage('error', 'Please enter a course name');
                        return;
                    }

                    await courseStore.updateCourse(course.id, course);
                };

                // Cancel Edit
                app.config.globalProperties.cancelEditCourse = function () {
                    courseStore.exitEditMode();
                };

                // Delete Course
                app.config.globalProperties.deleteCourse = async function (id) {
                    if (confirm('Are you sure you want to delete this course? All associated topics will also be deleted.')) {
                        await courseStore.deleteCourse(id);
                    }
                };

                // Add Topic
                app.config.globalProperties.addTopic = async function () {
                    const newTopic = { ...courseStore.newTopic };

                    // Basic validation
                    if (!newTopic.title) {
                        courseStore.setMessage('error', 'Please enter a topic title');
                        return;
                    }

                    if (!newTopic.course_id) {
                        courseStore.setMessage('error', 'Please select a course for this topic');
                        return;
                    }

                    await courseStore.createTopic(newTopic);
                };

                // Delete Topic
                app.config.globalProperties.deleteTopic = async function (id) {
                    if (confirm('Are you sure you want to delete this topic?')) {
                        await courseStore.deleteTopic(id);
                    }
                };

                // Assign Teacher to Topic
                app.config.globalProperties.assignTeacher = async function (topicId, teacherId) {
                    await courseStore.assignTeacherToTopic(topicId, teacherId);
                };

                // Load initial data
                app.config.globalProperties.loadCourses = function () {
                    console.log("Loading courses and topics data");
                    courseStore.fetchCourses();
                    courseStore.fetchTopics();
                };
            }
        }
    });
}