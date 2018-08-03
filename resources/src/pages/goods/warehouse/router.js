/*eslint no-inline-comments: "off"*/

export default {
  path: '/goods/warehouse',
  component: () => import('./main.vue'),
  children: [
    {
      // list
      path: '',
      name: 'warehouse',
      component: () => import(/* webpackChunkName: "warehouse" */'./warehouse.vue'),
    },
    {
      // edit
      path: 'edit/:goods_no/:status',
      name: 'goods-detail-warehouse',
      component: () => import(/* webpackChunkName: "goodsDetail" */'../goods-detail/goods-detail.vue'),
    },
  ],
}
