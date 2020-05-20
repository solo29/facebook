const state = {
    newsPosts: null,
    newsPostsStatus: null,
    postMessage: ""
};

const getters = {
    newsPosts: state => state.newsPosts,
    newsStatus: state => state.newsPostsStatus,
    postMessage: state => state.postMessage
};

const actions = {
    fetchNewsPosts({ commit }, title) {
        commit("setPostsStatus", "loading");

        axios
            .get("api/posts")
            .then(res => {
                commit("setPosts", res.data);
                commit("setPostsStatus", "success");
            })
            .catch(e => {
                console.error("Unable to fetch posts");
                commit("setPostsStatus", "error");
            });
    },
    postMessage({ commit, state }, title) {
        axios
            .post("api/posts", { body: state.postMessage })
            .then(res => {
                commit("pushPost", res.data);

                commit("updateMessage", "");
            })
            .catch(e => {
                console.error("Unable to fetch posts");
                commit("setPostsStatus", "error");
            });
    }
};

const mutations = {
    pushPost(state, post) {
        state.newsPosts.data.unshift(post);
    },
    setPosts(state, posts) {
        state.newsPosts = posts;
    },
    setPostsStatus(state, status) {
        state.newsPostsStatus = status;
    },
    updateMessage(state, message) {
        state.postMessage = message;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
