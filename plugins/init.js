import Vue from "vue";
import Cookie  from "js-cookie"
import vuetifyToast from 'vuetify-toast'
import VuetifyConfirm from 'vuetify-confirm'
Vue.use(VuetifyConfirm)
Vue.use({
  install() {
    Vue.prototype.$cookie = Cookie
    Vue.prototype.$toast = vuetifyToast
  }
})
