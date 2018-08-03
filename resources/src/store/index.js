/*eslint global-require: "off"*/
import Vue from 'vue'
import Vuex from 'vuex'
import actions from './actions'
import mutations from './mutations'
import view from './modules/view'
import user from './modules/user'

Vue.use(Vuex)

const store = new Vuex.Store({
  strict: process.env.NODE_ENV !== 'production',
  actions,
  mutations,

  modules: {
    user,
    view,
  },
})

// 使 action 和 mutation 成为可热重载模块
if (module.hot) {
  module.hot.accept([
    './mutations',
    './modules/view',
    './modules/user',
  ], () => {
    // 获取更新后的模块
    // 因为 babel 6 的模块编译格式问题，这里需要加上 `.default`
    const newMutations = require('./mutations').default
    const newModuleView = require('./modules/view').default
    const newModuleUser = require('./modules/user').default

    // 加载新模块
    store.hotUpdate({
      mutations: newMutations,
      modules: {
        user: newModuleView,
        view: newModuleUser,
      },
    })
  })
}

export default store
