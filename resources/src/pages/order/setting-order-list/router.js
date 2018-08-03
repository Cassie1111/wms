/*eslint no-inline-comments: "off"*/

export default {
  path: '/order/setting-order-list',
  name: 'setting-order-list',
  component: () => import(/* webpackChunkName: "orderList" */'./setting-order-list.vue'),
}
