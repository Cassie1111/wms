/*eslint no-inline-comments: "off"*/

export default {
  path: '/error/:id',
  name: 'error',
  components: {
    error: () => import(/* webpackChunkName: "error" */'./error.vue'),
  },
}
