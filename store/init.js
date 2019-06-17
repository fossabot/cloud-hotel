export default {
  state: {
    me: {},
    showPage: false
  },
  mutations: {
    SET_ME: (state, me) => state.me = me,
    SET_SHOW_PAGE: (state, page) => state.showPage = page,
  },
  getters: {
    ME: ({me}) => me,
    PERMISSIONS: ({me}) => {
      if (!!me && !!me.roles && !!me.roles.permissions) {
        const permissions = me.roles.permissions
        if (!!permissions) {
          return permissions
        }
      }
      return []
    },
    SHOW_PAGE: ({showPage}) => showPage
  }
}
