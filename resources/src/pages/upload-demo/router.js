/*eslint no-inline-comments: "off"*/

export default {
  path: '/upload-demo',
  component: () => import(/* webpackChunkName: "upload" */'./upload.vue'),
}
