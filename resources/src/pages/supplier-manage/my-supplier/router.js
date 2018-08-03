/*eslint no-inline-comments: "off"*/

export default {
  path: '/supplier',
  name: 'supplier-list',
  component: () => import(/* webpackChunkName: "supplier" */'./my-supplier.vue'),
}
