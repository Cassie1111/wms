import request from '@/utils/request'

export function getActivityList(params) {
  return request({
    url: '/api/marketing/activity',
    method: 'get',
    params,
  })
}

export function getActivityDetail(params) {
  return request({
    url: `/api/marketing/activity/${params.seckill_id}`,
    method: 'get',
    params,
  })
}

export function saveActivity(data) {
  return request({
    url: '/api/marketing/activity',
    method: 'post',
    data,
  })
}

export function getSeckillPrice(params) {
  return request({
    url: '/api/marketing/activity/create',
    method: 'get',
    params,
  })
}
