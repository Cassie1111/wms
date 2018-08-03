
/*eslint no-inline-comments: "off"*/

export default {
  path: '/resource-list',
  name: 'resource-list',
  component: () => import(/* webpackChunkName: "shop" */'./resource-list.vue'),
}
