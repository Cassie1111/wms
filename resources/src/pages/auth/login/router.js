/*eslint no-inline-comments: "off"*/

export default {
  path: '/login',
  name: 'login',
  components: {
    login: () => import(/* webpackChunkName: "login" */'./login.vue'),
  },
}
