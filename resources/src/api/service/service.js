import request from '@/utils/request'

// 获取"未读消息"数 --- 顶部导航的红点
export function getServiceInfo() {
  return request.get('/api/im/getUnReadConversationList')
}

// 获取"未读消息"用户列表
export function unreadList(params = {}) {
  return request.get('/api/im/unread', {
    params,
  })
}

// 获取"已读消息"用户列表
export function readList(params = {}) {
  return request.get('/api/im/read', {
    params,
  })
}

// 发送消息
export function pullMsg(params = {}) {
  return request.get('/api/im/pull', {
    params,
  })
}

// 发送消息
export function pushMsg(data = {}) {
  return request.post('/api/im/push', data)
}

// 释放会话
export function releaseConversation(params = {}) {
  return request.get('/api/im/release', {
    params,
  })
}

// 搜索聊天记录
export function searchMsg(params = {}) {
  return request.get('/api/im/search', {
    params,
  })
}

// 官方客服环信等配置信息
export function getConfig() {
  return request.get('/api/im/getConfig')
}
