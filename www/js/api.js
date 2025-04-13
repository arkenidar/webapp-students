/**
 * api.js - Handles API communication with the backend
 * This module encapsulates all API calls to maintain a clean separation of concerns
 */

const API = {
    BASE_URL: 'api.php',

    /**
     * Generic function to handle API requests
     * @param {string} endpoint - The API endpoint to call
     * @param {string} method - HTTP method (GET, POST, PUT, DELETE)
     * @param {Object} [data=null] - Optional data to send with the request
     * @returns {Promise} - Promise resolving to the API response
     */
    async request(endpoint, method, data = null) {
        let options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };

        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(`${this.BASE_URL}/${endpoint}`, options);

            // For debugging
            console.log(`API ${method} request to ${endpoint}: `, response.status);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: 'Unknown error' }));
                throw new Error(errorData.error || errorData.message || 'API request failed');
            }

            const responseData = await response.json();
            console.log("API Response:", responseData); // Debug log to see the response structure

            // Extract the actual data from the API response structure
            return responseData.data || responseData;
        } catch (error) {
            console.error(`API Error (${endpoint}):`, error);
            throw error;
        }
    },

    // Teachers API
    teachers: {
        getAll() {
            return API.request('teachers', 'GET');
        },

        create(teacherData) {
            return API.request('teachers', 'POST', teacherData);
        },

        update(id, teacherData) {
            return API.request(`teachers/${id}`, 'PUT', teacherData);
        },

        delete(id) {
            return API.request(`teachers/${id}`, 'DELETE');
        }
    },

    // Courses API
    courses: {
        getAll() {
            return API.request('courses', 'GET');
        },

        create(courseData) {
            return API.request('courses', 'POST', courseData);
        },

        update(id, courseData) {
            return API.request(`courses/${id}`, 'PUT', courseData);
        },

        delete(id) {
            return API.request(`courses/${id}`, 'DELETE');
        }
    },

    // Topics API
    topics: {
        getAll() {
            return API.request('topics', 'GET');
        },

        create(topicData) {
            return API.request('topics', 'POST', topicData);
        },

        assignTeacher(id, teacherId) {
            return API.request(`topics/${id}/assign`, 'PUT', { teacher_id: teacherId });
        },

        delete(id) {
            return API.request(`topics/${id}`, 'DELETE');
        }
    },

    // Students API
    students: {
        getAll() {
            return API.request('students', 'GET');
        },

        create(studentData) {
            return API.request('students', 'POST', studentData);
        },

        update(id, studentData) {
            return API.request(`students/${id}`, 'PUT', studentData);
        },

        delete(id) {
            return API.request(`students/${id}`, 'DELETE');
        }
    }
};