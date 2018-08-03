
/*eslint no-inline-comments: "off"*/

export default {
  path: '/distribution/distribution-detail',
  name: 'distribution-detail',
  component: () => import(/* webpackChunkName: distributionDetail*/'./distribution-detail.vue'),
}
