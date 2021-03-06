const state = {
    user: null,
    userStatus: null
};

const getters = {
    authUser: state => state.user,
    authUserStatus: state => state.userStatus,
    profileImage: state =>
        state.user.data.attributes.profile_image.data.attributes.path,
    coverImage: state =>
        state.user.data.attributes.profile_image.data.attributes.path
};

const actions = {
    fetchAuthUser({ commit }) {
        axios
            .get("/api/auth-user")
            .then(response => {
                commit("setAuthUser", response.data);
            })
            .catch(e => console.error(e))
            .finally(() => commit("setUserStatus", true));
    }
};

const mutations = {
    setAuthUser(state, user) {
        state.user = user;
    },
    setUserStatus(state, status) {
        state.userStatus = status;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
