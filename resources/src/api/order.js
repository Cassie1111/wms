import request from '@/utils/request'

export function getOrderDetail(purchaseNo) {
  const params = {
    purchase_no: purchaseNo,
  }

  return request({
    url: '/api/order/orderDetail',
    method: 'GET',
    params,
  })
}

export function getOrderList(params) {
  return request({
    url: '/api/order/orderList',
    method: 'GET',
    params,
  })
}

export function getSettingOrderList(params) {
  return request({
    url: '/api/order/settingOrderList',
    method: 'GET',
    params,
  })
}

export function getRefundList(params) {
  return request({
    url: '/api/order/refund',
    method: 'GET',
    params,
  })
}

export function editOrderDetail(postParam) {
  const params = {
    post_param: postParam,
  }

  return request.post('/api/order/orderDetail/edit', params)
}

export function cancelOrder(params) {
  return request({
    url: '/api/order/orderList',
    method: 'POST',
    params,
  })
}

export function outOrder(params) {
  return request({
    url: '/api/order/settingOrderList',
    method: 'POST',
    params,
  })
}

export function refundOrder(params) {
  return request({
    url: '/api/order/refundOrder',
    method: 'POST',
    params,
  })
}

export function getStorageOder(id) {
  return request({
    url: `/api/order/orderList/${id}`,
    method: 'get',
  })
}

export function changePrice(params) {
  return request.post(`/api/order/settingOrderList/changePrice`, params)
}
