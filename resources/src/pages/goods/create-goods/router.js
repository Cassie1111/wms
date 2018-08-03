/*eslint no-inline-comments: "off"*/

export default {
  path: '/goods/create-goods',
  component: () => import(/**/ './main.vue'),
  children: [
    {
      // category
      path: '',
      name: 'create-goods',
      component: () => import(/* webpackChunkName: "createGoods" */'./create-goods.vue'),
    },
    {
      // create
      path: 'create',
      name: 'goods-detail-create',
      component: () => import(/* webpackChunkName: "goodsDetail" */'../goods-detail/goods-detail.vue'),
    },
  ],
}
