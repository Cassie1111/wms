// 兼容低版本浏览器，使用方式：
// flexfill()
// css ===> .flex {-js-display: flex; display: flex;}

import debounce from 'debounce'
import flexibility from 'flexibility'

// 是否支持flex布局
function supportsFlexBox() {
  if (typeof supportsFlexBox.support !== 'undefined') return supportsFlexBox.support

  supportsFlexBox.support = true

  if (/MSIE|Trident/.test(navigator.userAgent)) {
    const node = document.createElement('div')

    if ('onreadystatechange' in node && !('draggable' in node)) {
      supportsFlexBox.support = false
    }
  }

  return supportsFlexBox.support
}

let onresizeTimeout
const doc = document

function resize() {
  if (!onresizeTimeout) {
    onresizeTimeout = setTimeout(() => {
      onresizeTimeout = null
      flexibility(doc.body)
    }, 1000 / 60)
  }
}

export default function flex() {
  if (supportsFlexBox()) return

  flexibility(doc.body)

  if (!flex.binding) {
    const onresizeHandler = debounce(resize, 300)

    window.addEventListener('resize', onresizeHandler)
    window.addEventListener('orientationchange', onresizeHandler)
  }

  flex.binding = true
}
