/*eslint no-param-reassign: "off"*/

// 用户模型，包括用户登陆状态和用户信息

const user = {
  state: {
    userInfo: {},
  },
  mutations: {
    setUserInfo(state, userInfo) {
      state.userInfo = userInfo
      localStorage.setItem('token', userInfo.token)
    },
  },
  getters: {
    accountType: state => state.userInfo.account_type,
  },
}

export default user
