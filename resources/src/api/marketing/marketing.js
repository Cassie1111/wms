// 营销中心相关接口
import request from '@/utils/request'

export function getGoodsList(params) {
  return request({
    url: '/api/marketing/getGoodsList',
    method: 'get',
    params,
  })
}

export function getSupplierList() {
  return request({
    url: '/api/marketing/getSupplierList',
    method: 'get',
  })
}

export function getBrandList() {
  return request({
    url: '/api/marketing/getBrandList',
    method: 'get',
  })
}
