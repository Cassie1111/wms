
/*eslint no-inline-comments: "off"*/

export default {
  path: '/banner-list/banner-add',
  name: 'banner-add',
  component: () => import(/* webpackChunkName: "shop" */'./add.vue'),
}
