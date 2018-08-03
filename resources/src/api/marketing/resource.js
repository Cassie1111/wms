import request from '@/utils/request'

export function getResourceList(params) {
  return request({
    url: '/api/marketing/resource',
    method: 'get',
    params,
  })
}

export function deleteResource(id) {
  return request.delete(`/api/marketing/resource/${id}`)
}

export function saveResource(data) {
  return request({
    url: '/api/marketing/resource',
    method: 'post',
    data,
  })
}

export function getResourceDetail(id) {
  return request.get(`/api/marketing/resource/${id}`)
}
