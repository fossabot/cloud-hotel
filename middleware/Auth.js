import Cookie from "js-cookie"

export default function ({store, route, redirect, $axios}) {
  const token = Cookie.get('token');
  if (token === undefined) {
    if (route.path !== "/login") {
      return redirect(301, '/login')
    }
  } else if (token !== undefined) {
    $axios.defaults.headers.common['Authorization'] = token;
    $axios.get('/me')
      .then(({data: {results}}) => store.commit('init/SET_ME', results))
      .finally(() => {
        store.commit('init/SET_SHOW_PAGE', true);
        if (route.path === "/login") {
          return redirect(301, '/')
        }
      })
  }
}
