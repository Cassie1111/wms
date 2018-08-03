
/*eslint no-inline-comments: "off"*/

export default {
  path: '/activity-manager/activity-add',
  name: 'activity-add',
  component: () => import(/* webpackChunkName: "shop" */'./add.vue'),
}
