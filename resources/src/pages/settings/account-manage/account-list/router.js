/*eslint no-inline-comments: "off"*/

export default {
  path: '/setting/account-manage',
  name: 'account-list',
  component: () => import(/* webpackChunkName: "accountManage" */'./account-list.vue'),
  props: true,
}
