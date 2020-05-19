const state = {
    user: null,
    posts: null,
    userStatus: null,
    postsStatus: null
};

const getters = {
    user: state => state.user,
    status: state => ({
        user: state.userStatus,
        posts: state.postsStatus
    }),
    posts: state => state.posts,

    friendship: state => state.user.data.attributes.friendship,
    friendButtonText: (state, getters, rootState) => {
        if (rootState.User.user.data.user_id === state.user.data.user_id) {
            return "";
        }

        if (getters.friendship === null) {
            return "Add Friend";
        } else if (
            getters.friendship.data.attributes.confirmed_at === null &&
            getters.friendship.data.attributes.friend_id ===
                rootState.User.user.data.user_id
        ) {
            return "Pending Friend Request";
        } else if (getters.friendship.data.attributes.confirmed_at !== null) {
            return "";
        }

        return "Accept";
    }
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
    fetchUser({ commit, dispatch }, userId) {
        commit("setUserStatus", "loading");
        axios
            .get("/api/users/" + userId)
            .then(response => {
                commit("setUser", response.data);
                commit("setUserStatus", "success");
            })
            .catch(e => {
                console.error(e);
                commit("setUserStatus", "error");
            });
    },
    sendFriendRequest({ commit, getters }, friendId) {
        if (getters.friendButtonText != "Add Friend") return;
        axios
            .post("/api/friend-request", { friend_id: friendId })
            .then(response => {
                commit("setUserFriendship", response.data);
            })
            .catch(e => {
                console.error(e);
            });
    },
    acceptFriendRequest({ commit }, userId) {
        axios
            .post("/api/friend-request-response", {
                user_id: userId,
                status: 1
            })
            .then(response => {
                commit("setUserFriendship", response.data);
            })
            .catch(e => {
                console.error(e);
            });
    },
    ignoreFriendRequest({ commit }, userId) {
        axios
            .delete("/api/friend-request-response/delete", {
                data: { user_id: userId }
            })
            .then(response => {
                commit("setUserFriendship", null);
            })
            .catch(e => {
                console.error(e);
            });
    }
};

const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setUserFriendship(state, friendship) {
        state.user.data.attributes.friendship = friendship;
    },
    setUserStatus(state, status) {
        state.userStatus = status;
    },
    setPostsStatus(state, status) {
        state.postsStatus = status;
    },
    setPosts(state, posts) {
        state.posts = posts;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
