/*eslint no-param-reassign: "off"*/

const drag = {
  bind(el, binding, vnode) {
    let positionX = 0
    let positionY = 0
    let mousedownX = 0
    let mousedownY = 0
    let mouseX = 0
    let mouseY = 0
    let moveX = 0
    let moveY = 0
    const target = el
    let isMove = false
    let handle = []

    if (binding.expression !== undefined) {
      handle = el.querySelectorAll(binding.value)
    } else {
      handle = []
      handle.push(el)
    }

    handle.forEach(dom => {
      dom.onmousedown = e => {
        isMove = true

        positionX = target.offsetLeft
        positionY = target.offsetTop
        mousedownX = e.pageX
        mousedownY = e.pageY

        return false
      }

      dom.addEventListener('mouseup', e => {
        isMove = false
      })
    })

    window.addEventListener('mousemove', e => {
      if (isMove) {
        mouseX = e.clientX
        mouseY = e.clientY

        moveX = positionX + mouseX - mousedownX
        moveY = positionY + mouseY - mousedownY

        target.style.left = `${moveX}px`
        if (moveY < target.offsetHeight / 2) {
          moveY = target.offsetHeight / 2
        }
        target.style.top = `${moveY}px`
      }

      return false
    })

    window.addEventListener('mouseup', () => {
      isMove = false
    })
  },
}

const install = function install(Vue) {
  Vue.directive('drag', drag)
}

if (window.Vue) {
  window.drag = drag
  window.Vue.use(install)
}

drag.install = install

export default drag
