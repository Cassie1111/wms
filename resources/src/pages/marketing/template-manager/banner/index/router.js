
/*eslint no-inline-comments: "off"*/

export default {
  path: '/banner-list',
  name: 'banner-list',
  component: () => import(/* webpackChunkName: "shop" */'./index.vue'),
}
