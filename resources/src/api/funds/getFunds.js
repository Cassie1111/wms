import request from '@/utils/request'

export function getFundList(params) {
  return request({
    url: '/api/funds/fundsList',
    method: 'GET',
    params,
  })
}

export function getFundDetail(id) {
  return request({
    url: `/api/funds/fundsList/${id}`,
    method: 'GET',
  })
}

export function setOrderStatus(params) {
  return request({
    url: `/api/funds/fundsList/setOrderStatus`,
    method: 'POST',
    params,
  })
}
