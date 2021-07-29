import {createNews, deleteNews, editNews, getNews} from "@/api/news";

const newsModule = {
    state: {
        news: [],
    },
    getters: {
        NEWS: s => s.news,
    },
    mutations: {
        SET_NEWS(state, payload) {
            state.news = payload.map(news => {
                news.image = news.news_image[0].image;
                return news;
            });
        },
        ADD_NEWS(state, payload) {
            state.news.push({
                image: payload.news_image[0].image,
                ...payload
            });
        },
        EDIT_NEWS(state, payload) {
            state.news = state.news.map(news => {
                if (news.id === payload.id) {
                    news = {
                        image: payload.news_image[0].image,
                        ...payload
                    }
                }
                return news;
            })
        },
        DELETE_NEWS(state, payload) {
            state.news = state.news.filter(news => news.id !== payload);
        }
    },
    actions: {
        async GET_NEWS({commit}) {
            const response = await getNews();
            commit('SET_NEWS', response.data);
        },
        async ADD_NEWS({commit}, payload) {
            const response = await createNews(payload);
            commit('ADD_NEWS', response.data);
            this.$toast.success('Новость добавлена!')
        },
        async EDIT_NEWS({commit}, payload) {
            const response = await editNews(payload);
            commit('EDIT_NEWS', response.data);
            this.$toast.success('Новость отредактирована!')
        },
        async DELETE_NEWS({commit}, payload) {
            await deleteNews(payload);
            commit('DELETE_NEWS', payload);
            this.$toast.success('Новость удалена!')
        }
    }
};

export default newsModule;
