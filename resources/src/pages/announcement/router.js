/*eslint no-inline-comments: "off"*/

export default {
  path: '/announcement/detial/:id',
  name: 'announcement',
  component: () => import(/* webpackChunkName: "announcement" */'./announcement-detial.vue'),
}
