/*eslint import/prefer-default-export: "off"*/

// 数字格式化，类似于PHP的number_format
export function numberFormat(originalNumber, decimals, decPoint, thousandsSep) {
  // 非数字、科学计数法过滤
  const number = `${originalNumber}`.replace(/[^0-9+\-Ee.]/g, '')

  const n = !Number.isFinite(Number(number)) ? 0 : Number(number)
  const prec = !Number.isFinite(Number(decimals)) ? 0 : Math.abs(decimals)
  const sep = typeof thousandsSep === 'undefined' ? ',' : thousandsSep
  const dec = typeof decPoint === 'undefined' ? '.' : decPoint
  let s = ''

  const toFixedFix = function toFixedFix(nFix, precFix) {
    const k = Math.pow(10, precFix)

    return `${(Math.round(nFix * k) / k).toFixed(precFix)}`
  }

  s = (prec ? toFixedFix(n, prec) : `${Math.round(n)}`).split('.')

  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
  }

  if ((s[1] || '').length < prec) {
    s[1] = s[1] || ''
    s[1] += new Array(prec - s[1].length + 1).join('0')
  }

  return s.join(dec)
}
