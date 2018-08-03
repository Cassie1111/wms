/*eslint no-inline-comments: "off"*/

export default {
  path: '/funds/:id',
  name: 'funds-detail',
  component: () => import(/* webpackChunkName: "fundsDetail"*/'./funds-detail.vue'),
}
