import axios from 'axios';

const API_URL = 'http://localhost:3002/api';

const api = axios.create({
  baseURL: API_URL,
});

// Add token to requests if it exists
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const auth = {
  register: (userData) => api.post('/auth/register', userData),
  login: (credentials) => api.post('/auth/login', credentials),
};

export const complaints = {
  getAll: (filters = {}) => api.get('/complaints', { params: filters }),
  getById: (id) => api.get(`/complaints/${id}`),
  create: (complaintData) => api.post('/complaints', complaintData),
  updateStatus: (id, status) => api.put(`/complaints/${id}`, { status }),
  addComment: (id, comment) => api.post(`/complaints/${id}/comments`, { comment_text: comment }),
};

export const admin = {
  getUsers: () => api.get('/admin/users'),
  updateUserRole: (userId, role) => api.put(`/admin/users/${userId}`, { role }),
  deleteUser: (userId) => api.delete(`/admin/users/${userId}`),
  getStatistics: () => api.get('/admin/statistics'),
}; 