/**
 * store.js - Central state management using Pinia
 * This module contains the Pinia stores for teachers, courses, and students
 */

// Teacher store
const useTeacherStore = Pinia.defineStore('teacher', {
    state: () => ({
        teachers: [],
        loading: false,
        message: null,
        newTeacher: {
            name: '',
            email: ''
        },
        editMode: false,
        editingTeacher: null
    }),

    actions: {
        async fetchTeachers() {
            this.loading = true;
            try {
                this.teachers = await API.teachers.getAll();
            } catch (error) {
                this.setMessage('error', 'Failed to load teachers: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        async createTeacher(teacherData) {
            try {
                await API.teachers.create(teacherData);
                this.fetchTeachers();
                this.setMessage('success', 'Teacher added successfully');
                this.resetNewTeacher();
            } catch (error) {
                this.setMessage('error', 'Failed to add teacher: ' + error.message);
            }
        },

        async updateTeacher(id, teacherData) {
            try {
                await API.teachers.update(id, teacherData);
                this.fetchTeachers();
                this.setMessage('success', 'Teacher updated successfully');
                this.exitEditMode();
            } catch (error) {
                this.setMessage('error', 'Failed to update teacher: ' + error.message);
            }
        },

        async deleteTeacher(id) {
            try {
                await API.teachers.delete(id);
                this.fetchTeachers();
                this.setMessage('success', 'Teacher deleted successfully');
            } catch (error) {
                this.setMessage('error', 'Failed to delete teacher: ' + error.message);
            }
        },

        enterEditMode(teacher) {
            this.editMode = true;
            this.editingTeacher = { ...teacher };
        },

        exitEditMode() {
            this.editMode = false;
            this.editingTeacher = null;
        },

        resetNewTeacher() {
            this.newTeacher = {
                name: '',
                email: ''
            };
        },

        setMessage(type, text) {
            this.message = { type, text };
            // Auto-clear success messages after 3 seconds
            if (type === 'success') {
                setTimeout(() => {
                    if (this.message && this.message.text === text) {
                        this.clearMessage();
                    }
                }, 3000);
            }
        },

        clearMessage() {
            this.message = null;
        }
    }
});

// Course store
const useCourseStore = Pinia.defineStore('course', {
    state: () => ({
        courses: [],
        topics: [],
        loading: {
            courses: false,
            topics: false
        },
        message: null,
        newCourse: {
            name: ''
        },
        newTopic: {
            title: '',
            course_id: ''
        },
        editMode: false,
        editingCourse: null
    }),

    actions: {
        async fetchCourses() {
            this.loading.courses = true;
            try {
                this.courses = await API.courses.getAll();
            } catch (error) {
                this.setMessage('error', 'Failed to load courses: ' + error.message);
            } finally {
                this.loading.courses = false;
            }
        },

        async fetchTopics() {
            this.loading.topics = true;
            try {
                this.topics = await API.topics.getAll();
            } catch (error) {
                this.setMessage('error', 'Failed to load topics: ' + error.message);
            } finally {
                this.loading.topics = false;
            }
        },

        async createCourse(courseData) {
            try {
                await API.courses.create(courseData);
                this.fetchCourses();
                this.setMessage('success', 'Course added successfully');
                this.resetNewCourse();
            } catch (error) {
                this.setMessage('error', 'Failed to add course: ' + error.message);
            }
        },

        async updateCourse(id, courseData) {
            try {
                await API.courses.update(id, courseData);
                this.fetchCourses();
                this.setMessage('success', 'Course updated successfully');
                this.exitEditMode();
            } catch (error) {
                this.setMessage('error', 'Failed to update course: ' + error.message);
            }
        },

        async deleteCourse(id) {
            try {
                await API.courses.delete(id);
                this.fetchCourses();
                this.fetchTopics(); // Refresh topics in case any were associated with this course
                this.setMessage('success', 'Course deleted successfully');
            } catch (error) {
                this.setMessage('error', 'Failed to delete course: ' + error.message);
            }
        },

        async createTopic(topicData) {
            try {
                await API.topics.create(topicData);
                this.fetchTopics();
                this.setMessage('success', 'Topic added successfully');
                this.resetNewTopic();
            } catch (error) {
                this.setMessage('error', 'Failed to add topic: ' + error.message);
            }
        },

        async deleteTopic(id) {
            try {
                await API.topics.delete(id);
                this.fetchTopics();
                this.setMessage('success', 'Topic deleted successfully');
            } catch (error) {
                this.setMessage('error', 'Failed to delete topic: ' + error.message);
            }
        },

        async assignTeacherToTopic(topicId, teacherId) {
            try {
                await API.topics.assignTeacher(topicId, teacherId);
                this.fetchTopics();
                this.setMessage('success', 'Teacher assigned successfully');
            } catch (error) {
                this.setMessage('error', 'Failed to assign teacher: ' + error.message);
            }
        },

        enterEditMode(course) {
            this.editMode = true;
            this.editingCourse = { ...course };
        },

        exitEditMode() {
            this.editMode = false;
            this.editingCourse = null;
        },

        resetNewCourse() {
            this.newCourse = {
                name: ''
            };
        },

        resetNewTopic() {
            this.newTopic = {
                title: '',
                course_id: this.courses.length > 0 ? this.courses[0].id : ''
            };
        },

        setMessage(type, text) {
            this.message = { type, text };
            // Auto-clear success messages after 3 seconds
            if (type === 'success') {
                setTimeout(() => {
                    if (this.message && this.message.text === text) {
                        this.clearMessage();
                    }
                }, 3000);
            }
        },

        clearMessage() {
            this.message = null;
        }
    }
});

// Student store
const useStudentStore = Pinia.defineStore('student', {
    state: () => ({
        students: [],
        loading: false,
        message: null,
        newStudent: {
            name: '',
            email: ''
        },
        editMode: false,
        editingStudent: null
    }),

    actions: {
        async fetchStudents() {
            this.loading = true;
            try {
                this.students = await API.students.getAll();
            } catch (error) {
                this.setMessage('error', 'Failed to load students: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        async createStudent(studentData) {
            try {
                await API.students.create(studentData);
                this.fetchStudents();
                this.setMessage('success', 'Student added successfully');
                this.resetNewStudent();
            } catch (error) {
                this.setMessage('error', 'Failed to add student: ' + error.message);
            }
        },

        async updateStudent(id, studentData) {
            try {
                await API.students.update(id, studentData);
                this.fetchStudents();
                this.setMessage('success', 'Student updated successfully');
                this.exitEditMode();
            } catch (error) {
                this.setMessage('error', 'Failed to update student: ' + error.message);
            }
        },

        async deleteStudent(id) {
            try {
                await API.students.delete(id);
                this.fetchStudents();
                this.setMessage('success', 'Student deleted successfully');
            } catch (error) {
                this.setMessage('error', 'Failed to delete student: ' + error.message);
            }
        },

        enterEditMode(student) {
            this.editMode = true;
            this.editingStudent = { ...student };
        },

        exitEditMode() {
            this.editMode = false;
            this.editingStudent = null;
        },

        resetNewStudent() {
            this.newStudent = {
                name: '',
                email: ''
            };
        },

        setMessage(type, text) {
            this.message = { type, text };
            // Auto-clear success messages after 3 seconds
            if (type === 'success') {
                setTimeout(() => {
                    if (this.message && this.message.text === text) {
                        this.clearMessage();
                    }
                }, 3000);
            }
        },

        clearMessage() {
            this.message = null;
        }
    }
});