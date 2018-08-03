/*eslint no-inline-comments: "off"*/

export default {
  path: '/retailer',
  name: 'retailer-list',
  component: () => import(/* webpackChunkName: "retailer-list" */'./retailer-list.vue'),
}
