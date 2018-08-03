
/*eslint no-inline-comments: "off"*/

export default {
  path: '/resource-list/resource-add',
  name: 'resource-add',
  component: () => import(/* webpackChunkName: "shop" */'./resource-add.vue'),
}
