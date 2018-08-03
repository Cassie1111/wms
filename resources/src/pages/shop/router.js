
/*eslint no-inline-comments: "off"*/

export default {
  path: '/shop',
  name: 'shop',
  component: () => import(/* webpackChunkName: "shop" */'./shop.vue'),
}
