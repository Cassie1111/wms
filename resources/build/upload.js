// 上传静态资源
/*eslint import/no-extraneous-dependencies: "off"*/

const OSS = require('ali-oss')
const co = require('co')
const path = require('path')
const fs = require('fs')

const STS = OSS.STS
const root = process.cwd()
const dirPath = path.join(root, 'public/dist')
const remotePath = `resource/scms/${process.env.NODE_ENV}/dist`

const suffixArr = [
  'js',
  'css',
  'jpg',
  'jpeg',
  'png',
  'gif',
  'ico',
  'eot',
  'woff',
  'woff2',
  'svg',
  'ttf',
]
const suffixReg = new RegExp(`\\.(?:${suffixArr.join('|')})$`, 'i')

function readFileList(resource, filesList) {
  if (!fs.existsSync(resource)) return

  const files = fs.readdirSync(resource)

  files.forEach(filename => {
    const fullPath = path.join(resource, filename)
    const stat = fs.statSync(fullPath)

    if (stat.isDirectory()) {
      // 递归
      readFileList(fullPath, filesList)
    } else {
      if (!suffixReg.test(fullPath)) return

      const obj = {
        from: fullPath,
        to: path.join(remotePath, fullPath.substring(dirPath.length)),
      }

      filesList.push(obj)
    }
  })
}

const filesList = []

// 同步获取文件列表
// 文件列表遍历
readFileList(dirPath, filesList)

// sts签名
// oss初始化
const accessKeyId = 'LTAI2r2Y0r7YWHus'
const accessKeySecret = 'mdqHGgr25eqnzgj4Y0v1WwPVGQsmwi'
const roleArn = 'acs:ram::1367150250403997:role/aliyunossofstaticdefaultrole'
const expiration = 900
const region = 'oss-cn-beijing'
const endpoint = 'https://oss-cn-beijing.aliyuncs.com'
const bucket = 'of-static'
const roleSessionName = 'scms'

const policy = {
  Statement: [
    {
      Action: ['oss:PutObject'],
      Effect: 'Allow',
      Resource: [`acs:oss:*:*:${bucket}`, `acs:oss:*:*:${bucket}/*`],
    },
  ],
  Version: '1',
}

const sts = new STS({
  accessKeyId,
  accessKeySecret,
})

co(function* upload() {
  const token = yield sts.assumeRole(roleArn, policy, expiration, roleSessionName)

  const client = new OSS({
    region,
    endpoint,
    accessKeyId: token.credentials.AccessKeyId,
    accessKeySecret: token.credentials.AccessKeySecret,
    stsToken: token.credentials.SecurityToken,
    bucket,
  })

  for (let i = 0, len = filesList.length; i < len; i++) {
    const result = yield client.put(filesList[i].to, filesList[i].from)

    if (!result || !result.res || result.res.statusCode !== 200) {
      console.log(result)
      console.log('upload fail')

      return false
    }
  }

  console.log('upload success')

  return true
}).catch(err => {
  console.log(err)
})
