
/*eslint no-inline-comments: "off"*/

export default {
  path: '/activity-manager/activity-list',
  name: 'activity-list',
  component: () => import(/* webpackChunkName: "shop" */'./activity-list.vue'),
}
