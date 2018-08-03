import clipboard from './clipboard'

const install = function install(Vue) {
  Vue.directive('clipboard', clipboard)
}

if (window.Vue) {
  window.clipboard = clipboard
  window.Vue.use(install)
}

clipboard.install = install

export default clipboard
