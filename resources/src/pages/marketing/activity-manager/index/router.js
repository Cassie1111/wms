
/*eslint no-inline-comments: "off"*/

export default {
  path: '/activity-manager',
  name: 'activity-manager',
  component: () => import(/* webpackChunkName: "shop" */'./index.vue'),
}
