/*eslint no-underscore-dangle: "off"*/
// 兼容低版本浏览器，使用方式：
// stickyfill()
// css样式
// .sticky {
//     position: -webkit-sticky;
//     position: sticky;
//     top: 0;
// }

import Stickyfill from 'stickyfilljs'

const className = '.sticky'

export default function sticky() {
  const nodes = [...document.querySelectorAll(className)]
  const stickies = Stickyfill.stickies
  const diffSticky = []

  stickies.forEach(sticky => {
    if (!nodes.some(node => sticky._node === node)) {
      diffSticky.push(sticky._node)
    }
  })

  // 删除不存在的节点
  Stickyfill.remove(diffSticky)
  Stickyfill.add(nodes)
}
