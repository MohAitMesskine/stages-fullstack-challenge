import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

const api = axios.create({
  baseURL: `${API_URL}/api`,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Articles
export const getArticles = (performanceTest = false) => {
  const params = performanceTest ? { performance_test: 'true' } : {};
  return api.get('/articles', { params });
};
export const getArticle = (id) => api.get(`/articles/${id}`);
export const searchArticles = (query) => api.get('/articles/search', { params: { q: query } });

   
export const createArticle = (data) => api.post('/articles', data);
export const updateArticle = (id, data) => api.put(`/articles/${id}`, data);
export const deleteArticle = (id) => api.delete(`/articles/${id}`);

// Comments
export const getComments = (articleId) => api.get(`/articles/${articleId}/comments`);
export const createComment = (data) => api.post('/comments', data);
export const updateComment = (id, data) => api.put(`/comments/${id}`, data);
export const deleteComment = (id) => api.delete(`/comments/${id}`);

// Auth
export const login = (credentials) => api.post('/login', credentials);
export const register = (data) => api.post('/register', data);
export const getMe = (userId) => api.get('/me', { params: { user_id: userId } });

// Stats
export const getStats = () => api.get('/stats');

// Image upload
export const uploadImage = (formData) => {
  return api.post('/upload', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });
};

export default api;

