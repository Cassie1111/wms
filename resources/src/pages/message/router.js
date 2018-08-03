/*eslint no-inline-comments: "off"*/

export default {
  path: '/message/entry',
  name: 'entry',
  component: () => import(/* webpackChunkName: "message" */'./entry.vue'),
}
