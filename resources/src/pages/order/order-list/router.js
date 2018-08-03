/*eslint no-inline-comments: "off"*/

export default {
  path: '/order/order-list',
  name: 'order-list',
  component: () => import(/* webpackChunkName: "orderList" */'./order-list.vue'),
}
