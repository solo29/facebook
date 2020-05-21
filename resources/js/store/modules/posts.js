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
    },
    likePost({ commit, state }, postId) {
        axios
            .post("/api/posts/" + postId + "/like")
            .then(res => {
                commit("pushLikes", { postId, data: res.data });

                commit("updateMessage", "");
            })
            .catch(e => {
                console.error(e);
            });
    },
    commentPost({ commit }, { postId, commentBody }) {
        axios
            .post("/api/posts/" + postId + "/comment", { body: commentBody })
            .then(res => {
                commit("pushComment", { postId, data: res.data });
            })
            .catch(e => {
                console.error(e);
            });
    }
};

const mutations = {
    pushPost(state, post) {
        state.newsPosts.data.unshift(post);
    },
    pushLikes(state, { postId, data }) {
        let index = state.newsPosts.data.findIndex(
            el => el.data.post_id == postId
        );

        if (index >= 0) {
            //console.log(index, data);
            state.newsPosts.data[index].data.attributes.likes = data;
        }
    },
    pushComment(state, { postId, data }) {
        let index = state.newsPosts.data.findIndex(
            el => el.data.post_id == postId
        );

        if (index >= 0) {
            //console.log(index, data);
            state.newsPosts.data[index].data.attributes.comments = data;
        }
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
