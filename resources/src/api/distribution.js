import request from '@/utils/request'

export function getDistributionDetail(tradeNo) {
  const params = {
    trade_no: tradeNo,
  }

  return request.get('/api/distribution/distributionDetail', {
    params,
  })
}

export function editComment(params) {
  return request.post('api/distribution/distributionComment', params)
}

export function launch(params) {
  return request.post('api/distribution/distributionLaunch', params)
}
