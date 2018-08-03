
/*eslint no-inline-comments: "off"*/

export default {
  path: '/goods',
  component: () => import(/* webpackChunkName: "sellingGoods" */'./selling-goods/selling-goods.vue'),
}
