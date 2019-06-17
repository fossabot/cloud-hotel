import Cookie from "js-cookie"
export default function ({ $axios, redirect, store, $message, router }) {
  $axios.defaults.headers.common['Content-Type'] = 'application/json';
  $axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  const token = Cookie.get('token');
  if(token !== undefined) {
    $axios.defaults.headers.common['Authorization'] = token;
  }
  $axios.onError(error => {
    if(!!error.response) {
      const code = parseInt(error.response.status);
      const {data:{type}} = error.response;
      if(code === 400 && type === "token") {
        Cookie.remove('token', {path: '/'});
        redirect(301, '/login')
      }
    }
  })
}
