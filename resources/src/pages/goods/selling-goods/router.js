/*eslint no-inline-comments: "off"*/

export default {
  path: '/goods/selling-goods',
  component: () => import(/**/ './main.vue'),
  children: [
    {
      // list
      path: '',
      name: 'selling-goods',
      component: () => import(/* webpackChunkName: "sellingGoods" */'./selling-goods.vue'),
    },
    {
      // edit
      path: 'edit/:goods_no',
      name: 'goods-detail-selling',
      component: () => import(/* webpackChunkName: "goodsDetail" */'../goods-detail/goods-detail.vue'),
    },
  ],
}
