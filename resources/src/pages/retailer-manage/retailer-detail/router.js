/*eslint no-inline-comments: "off"*/

export default {
  path: '/retailer/:id',
  name: 'retailer-detail',
  component: () => import(/* webpackChunkName: "retailer-detail" */'./retailer-detail.vue'),
}
