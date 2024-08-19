import axios from 'axios';

const apiClient = axios.create({
    baseURL: 'http://localhost:8000/api',
    withCredentials: false,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

export default {
    getProducts() {
        return apiClient.get('/products');
    },
    getProduct(id) {
        return apiClient.get('/products/' + id);
    },
    addProduct(product) {
        return apiClient.post('/products', product);
    },
    updateProduct(id, product) {
        return apiClient.put('/products/' + id, product);
    },
    deleteProduct(id) {
        return apiClient.delete('/products/' + id);
    }
};
