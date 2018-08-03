/*eslint no-inline-comments: "off"*/

export default {
  path: '/supplier/:id',
  name: 'supplier-detail',
  component: () => import(/* webpackChunkName: "supplier-detail" */'./supplier-detail.vue'),
}
