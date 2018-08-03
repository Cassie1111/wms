import request from '@/utils/request'

const message = 0

export function getMessageList(page, type) {
  let url

  if (type === message) {
    url = '/api/getMessageList'
  } else {
    url = '/api/getNoticeList'
  }
  const params = {
    page,
    count: 20,
  }

  return request.get(url, {
    params,
  })
}

// 修改读取状态
export function updateMessage() {
  return request.get('/api/updateMessage')
}

// 获取公告详情
export function getNoticeDetail(id) {
  const params = {
    id,
  }

  return request.get('/api/getNoticeDetail', {
    params,
  })
}
