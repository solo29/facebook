const state = {
    posts: null,
    postsStatus: null,
    postMessage: ""
};

const getters = {
    posts: state => state.posts,
    newsStatus: state => state.postsStatus,
    postMessage: state => state.postMessage
};

const actions = {
    fetchUserPosts({ commit }, userId) {
        commit("setPostsStatus", "loading");
        axios
            .get("/api/users/" + userId + "/posts")
            .then(res => {
                commit("setPosts", res.data);
                commit("setPostsStatus", "success");
            })
            .catch(e => {
                console.error("Unable to fetch posts");
                commit("setPostsStatus", "error");
            });
    },
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
        state.posts.data.unshift(post);
    },
    pushLikes(state, { postId, data }) {
        let index = state.posts.data.findIndex(el => el.data.post_id == postId);

        if (index >= 0) {
            //console.log(index, data);
            state.posts.data[index].data.attributes.likes = data;
        }
    },
    pushComment(state, { postId, data }) {
        let index = state.posts.data.findIndex(el => el.data.post_id == postId);

        if (index >= 0) {
            //console.log(index, data);
            state.posts.data[index].data.attributes.comments = data;
        }
    },

    setPosts(state, posts) {
        state.posts = posts;
    },
    setPostsStatus(state, status) {
        state.postsStatus = status;
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
