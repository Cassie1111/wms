/*eslint no-param-reassign: "off"*/

// 视图模型，包括公共组件的显示状态
const view = {
  state: {
    normalPage: false,
    leftMenu: [],
  },
  mutations: {
    changeNormalPage(state, isNormal) {
      state.normalPage = isNormal
    },

    setLeftMenu(state, leftMenu) {
      state.leftMenu = leftMenu
    },
  },
  getters: {
    home: state => state.leftMenu.filter(router => router.router_name === 'home'),
    supplier: state => state.leftMenu.filter(router => router.router_name === 'supplier-list'),
    retailer: state => state.leftMenu.filter(router => router.router_name === 'retailer-list'),
    sellingGoods: state => state.leftMenu.filter(router => router.router_name === 'selling-goods'),
    warehouse: state => state.leftMenu.filter(router => router.router_name === 'warehouse'),
    createGoods: state => state.leftMenu.filter(router => router.router_name === 'create-goods'),
    settingOrder: state => state.leftMenu.filter(router => router.router_name === 'setting-order-list'),
    order: state => state.leftMenu.filter(router => router.router_name === 'order-list'),
    refund: state => state.leftMenu.filter(router => router.router_name === 'refund'),
    banner: state => state.leftMenu.filter(router => router.router_name === 'banner-list'),
    activityConfig: state => state.leftMenu.filter(router => router.router_name === 'activity-config'),
    activityManager: state => state.leftMenu.filter(router => router.router_name === 'activity-manager'),
    resource: state => state.leftMenu.filter(router => router.router_name === 'resource-list'),
    pusher: state => state.leftMenu.filter(router => router.router_name === 'pusher'),
    funds: state => state.leftMenu.filter(router => router.router_name === 'funds'),
    account: state => state.leftMenu.filter(router => router.router_name === 'account-list'),
    service: state => state.leftMenu.filter(router => router.router_name === 'service'),
  },
}

export default view
