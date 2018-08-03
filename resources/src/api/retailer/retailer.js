
// 分销商相关接口
import request from '@/utils/request'

export function getList(params) {
  return request.get('/api/retailer', {
    params,
  })
}

export function getDetail(id) {
  const params = {}

  return request.get(`/api/retailer/${id}`, {
    params,
  })
}

export function editRetailer(data) {
  return request.post('/api/retailer', {
    ...data,
  })
}
