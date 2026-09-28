/**
 * WebDocTruyen - API Client Service
 */

const API_BASE = (window.APP_URL || '') + '/api';

const API = {
    getToken() {
        return localStorage.getItem('auth_token') || null;
    },

    setToken(token) {
        if (token) {
            localStorage.setItem('auth_token', token);
        } else {
            localStorage.removeItem('auth_token');
        }
    },

    async request(endpoint, options = {}) {
        const url = `${API_BASE}${endpoint}`;
        const headers = options.headers || {};

        const token = this.getToken();
        if (token && !headers['Authorization']) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        // Auto JSON content type if body is plain object and not FormData
        let body = options.body;
        if (body && !(body instanceof FormData) && typeof body === 'object') {
            headers['Content-Type'] = 'application/json';
            body = JSON.stringify(body);
        }

        try {
            const response = await fetch(url, {
                ...options,
                headers,
                body
            });

            const data = await response.json().catch(() => ({
                success: false,
                message: 'Không thể phân tích phản hồi từ máy chủ.'
            }));

            if (!response.ok || data.success === false) {
                const error = new Error(data.message || 'Đã có lỗi xảy ra');
                error.data = data;
                error.status = response.status;
                throw error;
            }

            return data;
        } catch (err) {
            console.error(`API Error [${endpoint}]:`, err);
            throw err;
        }
    },

    get(endpoint, params = {}) {
        const query = new URLSearchParams(params).toString();
        const url = query ? `${endpoint}?${query}` : endpoint;
        return this.request(url, { method: 'GET' });
    },

    post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: data
        });
    },

    put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST', // Use POST with method override or PUT
            body: data
        });
    },

    delete(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'DELETE',
            body: data
        });
    }
};

window.API = API;
