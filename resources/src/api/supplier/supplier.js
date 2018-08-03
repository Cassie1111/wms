
// 供应商相关接口
import request from '@/utils/request'

export function getList(params) {
  return request.get('/api/supplier', {
    params,
  })
}

export function getDetail(id) {
  const params = {}

  return request.get(`/api/supplier/${id}`, {
    params,
  })
}

export function getCountryCode() {
  const params = {}

  return request.get('/api/supplier/getCountryCode', {
    params,
  })
}

export function editSupplier(data) {
  return request.post('/api/supplier', {
    ...data,
  })
}
