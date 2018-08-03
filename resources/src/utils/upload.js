import plupload from 'plupload'
import * as env from '@/config/env'

// 目前只支持视频上传
let accessId = ''
let host = ''
let policyBase64 = ''
let signature = ''
let callbackbody = ''
let key = ''
let expire = 0
let gObjectName = ''

// 上传视频文件大小
let fileSize = 0

// random_name：随机文件名；local_name：默认文件名
const gObjectNameType = 'random_name'
let now = Date.parse(new Date()) / 1000

// 上传文件--目标路径
let pathName = 'register'

function sendRequest() {
  const url = `${env.BASE_API}/api/getUploadSign?path_name=${pathName}`
  const xmlhttp = new XMLHttpRequest()

  if (xmlhttp != null) {
    // 改为相关url
    // serverUrl = './php/get.php'
    xmlhttp.open('GET', url, false)
    xmlhttp.send(null)

    return xmlhttp.responseText
  }

  console.log('Your browser does not support XMLHTTP.')

  return false
}

// 上传文件名字是随机文件名, 后缀保留
function checkObjectRadio() {
  return gObjectNameType
}

function getSignature() {
  // 可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
  now = Date.parse(new Date()) / 1000

  if (expire < now + 3) {
    const obj = JSON.parse(sendRequest()).data

    host = obj.host
    policyBase64 = obj.policy
    accessId = obj.accessid
    signature = obj.signature
    expire = Number.parseInt(obj.expire, 10)
    callbackbody = obj.callback
    key = obj.dir

    return true
  }

  return false
}

function randomString(len) {
  const length = len || 32
  const chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678'
  const maxPos = chars.length
  let pwd = ''

  for (let i = 0; i < length; i++) {
    pwd += chars.charAt(Math.floor(Math.random() * maxPos))
  }

  return pwd
}

function getSuffix(filename) {
  const pos = filename.lastIndexOf('.')
  let suffix = ''

  if (pos !== -1) {
    suffix = filename.substring(pos)
  }

  return suffix
}

function calculateObjectName(filename) {
  if (gObjectNameType === 'local_name') {
    gObjectName += `${filename}`
  } else if (gObjectNameType === 'random_name') {
    const suffix = getSuffix(filename)

    gObjectName = key + randomString(10) + suffix
  }

  return ''
}

function getUploadedObjectName(filename) {
  if (gObjectNameType === 'local_name') {
    let tmpName = gObjectName

    tmpName = tmpName.replace(`${filename}`, filename)

    return tmpName
  } else if (gObjectNameType === 'random_name') {
    return gObjectName
  }

  return ''
}

function setUploadParam(up, filename, ret) {
  if (!ret) {
    ret = getSignature()
  }

  gObjectName = key

  if (filename !== '') {
    const suffix = getSuffix(filename)

    calculateObjectName(filename)
  }

  const newMultipartParams = {
    key: gObjectName,
    policy: policyBase64,
    OSSAccessKeyId: accessId,

    // 让服务端返回200,不然，默认会返回204
    success_action_status: '200',
    callback: callbackbody,
    signature,
  }

  up.setOption({
    url: host,
    multipart_params: newMultipartParams,
  })

  up.start()
}

function roundNumber(num, dec) {
  return Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec)
}

// 上传视频
const fileUploader = {
  settings: {},
  uploader: {},
  init(settings) {
    this.settings = settings

    pathName = settings.path_name

    let initialUploadTime = 0
    const uploader = new plupload.Uploader({
      runtimes: 'html5,flash,silverlight,html4',
      browse_button: settings.browse_button,
      multi_selection: false,
      container: document.getElementById(settings.container),

      // flash_swf_url : 'lib/plupload-2.1.2/js/Moxie.swf',
      // silverlight_xap_url : 'lib/plupload-2.1.2/js/Moxie.xap',
      url: 'https://com-ofashion-supply-chain.oss-cn-beijing.aliyuncs.com/',
      filters: {
        mime_types: settings.mime_types || [
          {
            title: 'Image files',
            extensions: 'jpg,jpeg,gif,png,bmp',
          },
        ],

        // mime_types: [
        //   // { title : "Image files", extensions : "jpg,jpeg,gif,png,bmp" },
        //   // { title : "Zip files", extensions : "zip,rar" },
        //   {
        //     title: 'video files',
        //     extensions: settings.extensions,
        //   },
        // ],

        // 最大只能上传300mb的文件
        max_file_size: settings.max_file_size,

        // 不允许选取重复文件
        prevent_duplicates: false,
      },

      init: {
        PostInit() {
          document.getElementById(settings.postfiles_button).onclick = () => {
            setUploadParam(uploader, '', false)

            return false
          }
        },

        FilesAdded(up, files) {
          // 文件上传按钮
          const button = document.getElementById(settings.postfiles_button)

          // 文件id存储在button元素     注意：！！！目前项目需求是仅支持同时单一文件上传
          button.dataset.id = files[0].id

          const reader = new FileReader()

          reader.readAsDataURL(files[0].getNative())

          reader.onload = theFile => {
            const image = new Image()

            image.src = theFile.target.result
            image.onload = function onload() {
              settings.width = this.width
              settings.height = this.height
            }
          }

          // 文件添加到队列里的回调
          settings.filesAdded()

          button.click()
        },

        BeforeUpload(up, file) {
          checkObjectRadio()
          setUploadParam(up, file.name, true)

          initialUploadTime = Number(new Date())
        },

        UploadProgress(up, file) {
          if (settings.uploadProgress) {
            // 文件已上传部分的大小，单位为字节
            let position = file.loaded

            // 文件的原始大小，单位为字节
            const origSize = file.origSize

            // 视频文件大小，回调传回去 M为单位
            fileSize = (origSize / 1000 / 1024).toFixed(1)

            // 文件已上传部分所占的百分比，如50就代表已上传了50%
            const percent = file.percent

            // 在 firefox 下， 大文件会有问题的，progress事件的e.loaded初始值从 e.total开始的...........
            if (position > origSize) {
              position -= origSize
            }

            Math.round((position / origSize) * 100)

            const currentTime = Number(new Date())
            const uTime = Math.ceil(currentTime - initialUploadTime) / 1000
            const rate = Math.floor(roundNumber(position / uTime / 1024, 2))

            settings.uploadProgress(`${file.percent}%${rate}KB/s`)
          }
        },

        FileUploaded(up, file, info) {
          if (info.status === 200) {
            // document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '
            // upload to oss success, object name:' + getUploadedObjectName(file.name);
            settings.success({
              path_full: `${env.IMG_URL}/${getUploadedObjectName(file.name)}`,
              path: `/${getUploadedObjectName(file.name)}`,
              width: settings.width,
              height: settings.height,
            })
          } else {
            // document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
            settings.error(info.response)
          }
        },

        Error(up, err) {
          let msg = ''

          if (err.code === -600) {
            // document.getElementById('console').appendChild(document.createTextNode("\n选择的文件太大了,
            // 可以根据应用情况，在upload.js 设置一下上传的最大大小"));
            msg = `选择的文件太大了，不要超过 ${settings.max_file_size}`
          } else if (err.code === -601) {
            // document.getElementById('console').appendChild(document.createTextNode("\n选择的文件后缀不对,
            // 可以根据应用情况，在upload.js进行设置可允许的上传文件类型"));
            msg = `选择的文件类型后缀不对，现支持： ${settings.extensions}`
          } else if (err.code === -602) {
            // document.getElementById('console').appendChild(document.createTextNode("\n这个文件已经上传过一遍了"));
            msg = '这个文件已经上传过一遍了'
          } else {
            // document.getElementById('console').appendChild(document.createTextNode("\nError xml:" + err.response));
            msg = `Error xml：${err.response}`
          }

          settings.error(msg)
        },
      },
    })

    this.uploader = uploader

    uploader.init()
  },
  cancelUpload() {
    try {
      // 文件上传按钮
      const button = document.getElementById(this.settings.postfiles_button)
      const id = button.dataset.id

      // 通过id来获取文件对象
      const file = this.uploader.getFile(id)

      // 停止队列中的文件上传
      this.uploader.stop()

      // 从上传队列中移除文件
      this.uploader.removeFile(file)
    } catch (e) {
      console.log(e)
    }
  },
}

export default fileUploader
