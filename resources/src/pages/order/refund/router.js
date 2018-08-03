/*eslint no-inline-comments: "off"*/

export default {
  path: '/order/refund',
  name: 'refund',
  component: () => import(/* webpackChunkName: "orderList" */'./refund.vue'),
}
