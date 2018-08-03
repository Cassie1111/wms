<template>
  <div>
    <!--上传图片-->
    <div class="upload-area" id="upload-area">
      <label id="upload-trigger" for="upload-files">上传照片</label>
      <input id="upload-files" type="file">
    </div>
    <!--图片部分-->
    <div class="img-area" v-if="imgList">
      <div class="img-area-item" v-for="item in imgList">
        <img :src="item.path_full"/>
      </div>
    </div>
    <!--图片预览-->
    <div>
      <picturePreview :pictureList="imgList"></picturePreview>
    </div>

  </div>
</template>
<script>
  import uploader from '@/utils/upload'
  import picturePreview from '../../components/pages/picture-preview/picture-preview'

  export default {
    name: 'demo',
    components: {
      picturePreview,
    },
    data() {
      return {
        imgList: [],
      }
    },
    mounted() {
      // 初始化上传
      const uploadSetting = {
        browse_button: 'upload-trigger',
        container: 'upload-area',
        postfiles_button: 'upload-files',
        path_name: 'register',
        max_file_size: '2M',
        filesAdded: () => {
          this.$message({
            showClose: true,
            message: '上传中',
            type: 'warning',
            onClose: () => {
              uploader.cancelUpload()
            },
          })
        },

        // 上传成功回调
        success: data => {
          this.$message.success('上传成功')
          this.imgList.push(data)
        },

        // 上传失败回调：包括前后端对上传的文件验证不通过、前后端网络异常
        error: msg => {
          this.$message.error(msg)
        },

        // 非必选项
        complete() {
          //
        },
      }

      uploader.init(uploadSetting)
    },
  }
</script>
<style scoped lang="scss">
  .upload-area {
    position: relative;
    label {
      padding: 5px 8px;
      border: 1px solid #ddd;
    }
    input {
      position: absolute;
      top: 2px;
      left: 0;
      opacity: 0;
      z-index: -1;
    }
  }

  .img-area {
    display: flex;
    .img-area-item {
      width: 100px;
      height: 100px;
      img {
        width: 100%;
        height: 100%;
      }
    }
  }
</style>