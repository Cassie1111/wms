
/*eslint no-inline-comments: "off"*/

export default {
  path: '/banner-list/activity-config',
  name: 'activity-config',
  component: () => import(/* webpackChunkName: "shop" */'./index.vue'),
}
