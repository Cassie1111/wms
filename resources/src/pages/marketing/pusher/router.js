/*eslint no-inline-comments: "off"*/

export default {
  path: '/pusher',
  name: 'pusher',
  component: () => import(/* webpackChunkName: "pusher" */'./pusher.vue'),
}
