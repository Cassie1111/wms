
const Obj = Object
const proto = Obj.prototype
const getProto = Obj.getPrototypeOf

function isObject(obj) {
  return obj && getProto(obj) == proto
}

function isArray(obj) {
  return Array.isArray(obj)
}

const hasOwn = proto.hasOwnProperty

function serialize(params, obj, traditional, scope) {
  const array = isArray(obj)
  const hash = isObject(obj)

  for (let key in obj) {
    if (hasOwn.call(obj, key)) {
      const value = obj[key]

      if (scope) {
        key = traditional ? scope
          : `${scope}[${hash || isObject(value) || isArray(value) ? key : ''}]`
      }

      // handle data in serializeArray() format
      if (!scope && array) params.add(value.name, value.value)

      // recurse into nested objects
      else if (isArray(value) || (!traditional && isObject(value))) serialize(params, value, traditional, key)
      else params.add(key, value)
    }
  }
}

export function param(obj, traditional) {
  const params = []
  const encode = encodeURIComponent

  params.add = function (key, value) {
    if (typeof value === 'function') value = value()
    if (value == null) value = ''

    this.push(`${encode(key)}=${encode(value)}`)
  }

  serialize(params, obj, traditional)

  return params.join('&').replace(/%20/g, '+')
}
