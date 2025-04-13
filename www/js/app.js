/**
 * app.js - Main application entry point
 * This module initializes the Vue application and coordinates all the modules
 */

// Wait until all scripts are loaded before initialization
window.addEventListener('load', () => {
    console.log("Window loaded, initializing application...");

    // Create Pinia instance
    const pinia = Pinia.createPinia();

    // Create Vue app
    const app = Vue.createApp({
        data() {
            return {
                activeTab: 'teachers', // Default active tab
                initialized: {
                    teachers: false,
                    courses: false,
                    students: false
                }
            };
        },
        methods: {
            setActiveTab(tab) {
                console.log("Switching to tab:", tab);
                this.activeTab = tab;

                // Load data for the selected tab if not already initialized
                switch (tab) {
                    case 'teachers':
                        if (!this.initialized.teachers) {
                            this.loadTeachers();
                            this.initialized.teachers = true;
                        }
                        break;
                    case 'courses':
                        if (!this.initialized.courses) {
                            this.loadCourses();
                            this.initialized.courses = true;
                        }
                        break;
                    case 'students':
                        if (!this.initialized.students) {
                            this.loadStudents();
                            this.initialized.students = true;
                        }
                        break;
                }
            },
            // These methods will be properly connected to the store methods once the app is mounted
            loadTeachers() {
                if (this.$teacherStore) {
                    console.log("Loading teachers data from Vue app method");
                    this.$teacherStore.fetchTeachers();
                } else {
                    console.error("Teacher store not initialized!");
                }
            },
            loadCourses() {
                if (this.$courseStore) {
                    console.log("Loading courses data from Vue app method");
                    this.$courseStore.fetchCourses();
                    this.$courseStore.fetchTopics();
                } else {
                    console.error("Course store not initialized!");
                }
            },
            loadStudents() {
                if (this.$studentStore) {
                    console.log("Loading students data from Vue app method");
                    this.$studentStore.fetchStudents();
                } else {
                    console.error("Student store not initialized!");
                }
            }
        },
        mounted() {
            console.log("Vue app mounted, active tab:", this.activeTab);

            // Initialize the stores as soon as possible after mounting
            if (useTeacherStore) {
                const teacherStore = useTeacherStore();
                this.$teacherStore = teacherStore;
            }

            if (useCourseStore) {
                const courseStore = useCourseStore();
                this.$courseStore = courseStore;
            }

            if (useStudentStore) {
                const studentStore = useStudentStore();
                this.$studentStore = studentStore;
            }

            // Load initial data for the default tab
            this.setActiveTab(this.activeTab);
        }
    });

    // Use Pinia with Vue app (must be before setup modules)
    app.use(pinia);

    // Set up all modules
    console.log("Setting up modules");
    setupTeachersModule(app);
    setupCoursesModule(app);
    setupStudentsModule(app);

    // Mount the application
    console.log("Mounting Vue app to #app element");
    app.mount('#app');
});