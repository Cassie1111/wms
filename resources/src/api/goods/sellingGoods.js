// 分销中的商品相关接口
import request from '@/utils/request'

export function getGoodsList(params = {}) {
  return request.get('/api/goods/goodsList', {
    params,
  })
}

export function getCates() {
  return request.get('/api/goods/getCates')
}

export function getSupplierList() {
  return request.get('/api/goods/getSupplierList')
}

export function updatePrice(data = {}) {
  return request.post('/api/goods/updatePrice', data)
}

export function unshelve(data = {}) {
  return request.post('/api/goods/unshelve', data)
}

export function exportProductList(data = {}) {
  return request.post('/api/goods/exportProductList', data)
}

export function exportAllProductList(data = {}) {
  return request.post('/api/goods/exportAllProductList', data)
}

export function syncOfGoodsInfo(data = {}) {
  return request.post('/api/goods/syncOfGoodsInfo', data)
}

export function exportGoods(data = {}) {
  return request.post('/api/goods/importGoods', data)
}
