import Vue from 'vue'
import Router from 'vue-router'
import store from '@/store/index'
import request from '@/utils/request'

const routes = require.context('../pages/', true, /router\.js$/)

Vue.use(Router)

const router = new Router({
  mode: 'history',

  routes: (r => {
    const routers = r.keys().map(key => r(key).default)

    return routers
  })(routes),
})

// 判断是否登陆
const checkLogin = () => request({
  url: '/api/auth/checkLogin',
  method: 'get',
})

function isExist(routerList, routerName) {
  if (routerList.length < 1) return false

  for (let i = 0; i < routerList.length; i++) {
    if (routerList[i].router_name === routerName) return true
  }

  return false
}

function hasPermission(routerList, routerName) {
  let permitted = false

  switch (routerName) {
    case 'supplier-detail':
      permitted = isExist(routerList, 'supplier-list')
      break
    case 'retailer-detail':
      permitted = isExist(routerList, 'retailer-list')
      break
    case 'goods-detail-selling':
      permitted = isExist(routerList, 'selling-goods')
      break
    case 'goods-detail-warehouse':
      permitted = isExist(routerList, 'warehouse')
      break
    case 'goods-detail-create':
      permitted = isExist(routerList, 'create-goods')
      break
    case 'funds-detail':
      permitted = isExist(routerList, 'funds')
      break
    case 'purchase-order-detail':
    case 'order-detail':
      permitted = isExist(routerList, 'order-list')
      break
    case 'distribution-detail':
      permitted = isExist(routerList, 'setting-order-list')
      break
    case 'banner-add':
      permitted = isExist(routerList, 'banner-list')
      break
    case 'resource-add':
      permitted = isExist(routerList, 'resource-list')
      break
    case 'activity-list':
    case 'activity-add':
      permitted = isExist(routerList, 'activity-manager')
      break
    case 'add-account':
    case 'edit-account':
      permitted = isExist(routerList, 'account-list')
      break
    case 'entry':
    case 'new-link':
    case 'announcement':
      permitted = true
      break
    default:
      permitted = isExist(routerList, routerName)
      break
  }

  return permitted
}

function goFirstPermitedPage(leftMenu, accountType, to, next) {
  const currentRouterName = to.name

  // 主账号
  if (accountType === 1) {
    if (currentRouterName === 'login' || currentRouterName === 'register') {
      next('/')
    } else {
      next()
    }
  } else {
    // 当前访问路由是否有权限
    const permitted = hasPermission(leftMenu, currentRouterName)

    if (permitted) {
      next()
    } else {
      next({
        name: leftMenu[0].router_name,
      })
    }
  }
}

// 判断是否为常规页面
function checkPage(routeName) {
  switch (routeName) {
    case 'service':
    case 'new-link':
      store.commit('changeNormalPage', false)
      break
    default:
      store.commit('changeNormalPage', true)
  }
}

router.beforeEach((to, from, next) => {
  checkLogin().then(res => {
    const leftMenu = res.left_menu
    const userInfo = res.data
    const accountType = userInfo.account_type

    // 登陆成功
    store.commit('setUserInfo', userInfo)
    store.commit('setLeftMenu', leftMenu)
    checkPage(to.name)
    goFirstPermitedPage(leftMenu, accountType, to, next)
  }).catch(err => {
    // TODO code值改为常量配置
    // 已登录
    if (err.code !== 'A401' && err.code !== 40001) {
      next()

      return
    }

    // 未登陆，要访问的页面不是登陆页面
    store.commit('changeNormalPage', false)

    if (to.name === 'login') {
      next()
    } else {
      next('/login')
    }
  })
})

export default router
