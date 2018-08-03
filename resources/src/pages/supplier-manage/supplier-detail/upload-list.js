
/*eslint object-curly-newline: "off"*/
/*eslint no-param-reassign: "off"*/
// puupload上传插件配置
import uploader from '@/utils/upload'
import { Message } from 'element-ui'

const uploaderSetting = {
  max_file_size: '10M',
  path_name: 'register',

  // 上传失败回调：包括前后端对上传的文件验证不通过、前后端网络异常
  error(msg) {
    Message({
      message: msg,
      type: 'err',
    })
  },

  // 非必选项
  complete() {
    //
  },
}

const uploader1 = { ...uploader }
const uploader2 = { ...uploader }
const uploader3 = { ...uploader }
const uploader4 = { ...uploader }
const uploader5 = { ...uploader }
const uploader6 = { ...uploader }
const uploader7 = { ...uploader }

const uploaderSetting1 = { ...uploaderSetting }
const uploaderSetting2 = { ...uploaderSetting }
const uploaderSetting3 = { ...uploaderSetting }
const uploaderSetting4 = { ...uploaderSetting }
const uploaderSetting5 = { ...uploaderSetting }
const uploaderSetting6 = { ...uploaderSetting }
const uploaderSetting7 = { ...uploaderSetting }

const uploadList = [
  {
    el: 'bus',
    uploader,
    uploaderSetting,
  },
  {
    el: 'tax',
    uploader: uploader1,
    uploaderSetting: uploaderSetting1,
  },
  {
    el: 'card',
    uploader: uploader2,
    uploaderSetting: uploaderSetting2,
  },
  {
    el: 'bank',
    uploader: uploader3,
    uploaderSetting: uploaderSetting3,
  },
  {
    el: 'payment',
    uploader: uploader4,
    uploaderSetting: uploaderSetting4,
  },
  {
    el: 'trademark',
    uploader: uploader5,
    uploaderSetting: uploaderSetting5,
  },
  {
    el: 'link',
    uploader: uploader6,
    uploaderSetting: uploaderSetting6,
  },
  {
    el: 'authorization',
    uploader: uploader7,
    uploaderSetting: uploaderSetting7,
  },
]

// 上传插件初始化
uploadList.forEach(item => {
  item.uploaderSetting.browse_button = `upload-${item.el}-trigger`
  item.uploaderSetting.container = `upload-${item.el}-area`
  item.uploaderSetting.postfiles_button = `upload-${item.el}-files`

  item.uploaderSetting.filesAdded = () => {
    Message({
      showClose: true,
      message: '上传中',
      type: 'warning',
      onClose: () => {
        item.uploader.cancelUpload()
      },
    })
  }
})

export default uploadList
