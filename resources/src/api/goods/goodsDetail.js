// 商品详情相关接口

import request from '@/utils/request'

export function getGoodsDetail(params = {}) {
  return request.get('/api/goods/getGoodsDetail', {
    params,
  })
}

export function getQuoteInfoByCate(params = {}) {
  return request.get('/api/goods/getQuoteInfoByCate', {
    params,
  })
}

export function getBrands(params = {}) {
  return request.get('/api/goods/getBrands', {
    params,
  })
}

export function getGoodsPropertyList(params = {}) {
  return request.get('/api/goods/getGoodsPropertyList', {
    params,
  })
}

export function editProductDetail(data = {}) {
  return request.post('/api/goods/editProductDetail', data)
}
