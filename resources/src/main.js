// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import 'promise-polyfill/src/polyfill'
import Vue from 'vue'
import Element from 'element-ui'
import './assets/styles/app.scss'
import App from './App'
import store from './store'
import router from './router'
import * as filters from './filters'

Vue.use(Element)
Vue.config.productionTip = false

// 注册过滤器
Object.keys(filters).forEach(key => {
  Vue.filter(key, filters[key])
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  store,
  router,
  template: '<App/>',
  components: {
    App,
  },
})
