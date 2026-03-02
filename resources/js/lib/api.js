import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

api.interceptors.response.use(
    (response) => response,
    (error) => Promise.reject(error),
);

export function apiErrorMessage(error) {
    if (error?.response?.data?.message) {
        return String(error.response.data.message);
    }

    if (error?.message) {
        return String(error.message);
    }

    return 'Une erreur est survenue.';
}

export default api;
