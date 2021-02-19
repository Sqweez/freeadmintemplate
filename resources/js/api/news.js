import axios from 'axios';

export async function getNews() {
    return await axios.get('/api/v2/news');
}

export async function createNews(news) {
    return await axios.post('/api/v2/news', news);
}

export async function editNews(news) {
    return await axios.patch(`/api/v2/news/${news.id}`, news);
}

export async function deleteNews(id) {
    return await axios.delete(`/api/v2/news/${id}`);
}
