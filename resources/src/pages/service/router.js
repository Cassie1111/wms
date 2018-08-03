/*eslint no-inline-comments: "off"*/

export default {
  path: '/service',
  name: 'service',

  components: {
    service: () => import(/* webpackChunkName: "service" */'./service'),
  },
}
