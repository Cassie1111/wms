/*eslint import/prefer-default-export: "off"*/

// 时间格式化
// TODO 补充参数、返回值说明
export function timeFormat(time, originalFormat = 'yyyy-MM-dd hh:mm:ss') {
  let date

  if (time instanceof Date) {
    date = time
  } else {
    date = new Date(String(time).length === 10 ? time * 1000 : time)
  }

  const formatObj = {
    'M+': date.getMonth() + 1,
    'd+': date.getDate(),
    'h+': date.getHours(),
    'm+': date.getMinutes(),
    's+': date.getSeconds(),
    'q+': Math.floor((date.getMonth() + 3) / 3),
    S: date.getMilliseconds(),
  }

  let format = originalFormat

  if (/(y+)/.test(originalFormat)) {
    format = originalFormat.replace(RegExp.$1, String(date.getFullYear()).substr(4 - RegExp.$1.length))
  }

  for (const k in formatObj) {
    if (new RegExp(`(${k})`).test(format)) {
      format = format.replace(
        RegExp.$1,
        RegExp.$1.length === 1 ? formatObj[k] : `00${formatObj[k]}`.substr(`${formatObj[k]}`.length)
      )
    }
  }

  return format
}
