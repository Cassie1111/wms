/*eslint no-inline-comments: "off"*/

export default {
  path: '/funds',
  name: 'funds',
  component: () => import(/* webpackChunkName: "funds"*/'./funds.vue'),
}
