const state = {
    title: "Welcome"
};

const getters = {
    pageTitle: state => state.title
};

const actions = {
    changePageTitle({ commit }, title) {
        commit("setPageTitle", title);
    }
};

const mutations = {
    setPageTitle(state, title) {
        state.title = title + " | Facebook";

        document.title = state.title;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
