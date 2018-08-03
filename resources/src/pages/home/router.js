
/*eslint no-inline-comments: "off"*/

export default {
  path: '/',
  name: 'home',
  component: () => import(/* webpackChunkName: "home" */'./home.vue'),
}
