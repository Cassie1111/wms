/*eslint no-inline-comments: "off"*/

export default {
  path: '/new-link',
  name: 'new-link',
  components: {
    newLink: () => import(/* webpackChunkName: "newLink" */'./new-link.vue'),
  },
}
