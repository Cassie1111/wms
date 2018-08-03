
/*eslint no-inline-comments: "off"*/

export default {
  path: '/order/order-detail',
  name: 'order-detail',
  component: () => import(/* webpackChunkName: orderDetail*/'./order-detail.vue'),
}
