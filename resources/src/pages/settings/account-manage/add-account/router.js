/*eslint no-inline-comments: "off"*/

export default {
  path: '/setting/account-manage',
  component: () => import('./main.vue'),
  children: [
    {
      path: 'add-account',
      name: 'add-account',
      component: () => import(/* webpackChunkName: "addAccount" */'./children/add-account.vue'),
    },
    {
      path: 'edit-account/:userNo',
      name: 'edit-account',
      component: () => import(/* webpackChunkName: "editAccount" */'./children/add-account.vue'),
      props: true,
    },
  ],
}
