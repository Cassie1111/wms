import request from '@/utils/request'

// 获取轮播列表
export function getBannerList() {
  return request.get('/api/marketing/banner')
}

// 获取轮播详情
export function getBannerDetail(id) {
  return request.get(`/api/marketing/banner/${id}`)
}

export function deleteBanner(id) {
  return request.delete(`/api/marketing/banner/${id}`)
}

export function saveBanner(data) {
  return request({
    url: '/api/marketing/banner',
    method: 'post',
    data,
  })
}

export function saveActivityConfig(data) {
  return request({
    url: '/api/marketing/editActivityConfigDetail',
    method: 'post',
    data,
  })
}

export function getActivityConfigDetail(params) {
  return request({
    url: '/api/marketing/getActivityConfigDetail',
    method: 'get',
    params,
  })
}

export function getConfigList() {
  return request({
    url: '/api/marketing/getActivityConfigList',
    method: 'get',
  })
}
