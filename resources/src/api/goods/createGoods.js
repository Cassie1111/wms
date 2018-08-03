// 创建新商品相关接口

import request from '@/utils/request'

export function searchProductQuoteType(params = {}) {
  return request.get('/api/goods/searchProductQuoteType', {
    params,
  })
}

export function readRemindInfo(announcementId) {
  const data = {
    announcement_id: announcementId,
  }

  return request.post('/api/goods/readRemindInfo', data)
}

export function getLastestAnnouncement(data = {}) {
  return request.get('/api/goods/getLastestAnnouncement', {
    data,
  })
}
