
/*eslint no-inline-comments: "off"*/

export default {
  path: '/order/purchase-order-detail',
  name: 'purchase-order-detail',
  component: () => import(/* webpackChunkName: "order" */'./purchase-order-detail.vue'),
}
