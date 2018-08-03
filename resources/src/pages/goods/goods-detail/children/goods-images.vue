<template>
  <div class="table-row">
    <div class="row-label">商品图片</div>
    <div class="row-content row-content-bg">
      <div class="table-row">
        <div class="row-label">商品主图</div>
        <div class="row-content of_flex_align_center">
          商品主图大小不能超过3MB；最好为白底图，至少上传1张，最多可上传5张。
        </div>
      </div>

      <div class="table-row">
        <div class="row-label"></div>
        <div class="row-content">
          <ul class="image-list">
            <li class="image-item" v-for="(cover,index) in coverList"
                @mouseover="showMask(index, 'cover')"
                @mouseleave="hideMask(index, 'cover')"
            >
              <img :src="cover.path_full" alt="">
              <div class="img-mask" :class="{show:index === coverIndex}">
                <i class="el-icon-delete" @click="removeCoverImg(index)"></i>
              </div>
            </li>
            <li class="image-item" v-if="isCoverLoading">
              <div class="image-loading" v-loading="true"></div>
            </li>
            <li v-show="coverList.length < 5">
              <div class="upload-area" id="upload-cover-area">
                <label id="upload-cover-trigger" for="upload-cover-files"></label>
                <i class="el-icon-plus"></i>
                <input id="upload-cover-files" type="file">
              </div>
            </li>
          </ul>
        </div>
      </div>

      <div class="table-row">
        <div class="row-label">商品详情图</div>
        <div class="row-content of_flex_align_center"></div>
      </div>

      <div class="table-row">
        <div class="row-label"></div>
        <div class="row-content">
          <ul class="image-list">
            <li class="image-item" v-for="(detail,index) in detailList"
                @mousemove="showMask(index, 'detail')"
                @mouseleave="hideMask(index, 'detail')"
            >
              <img :src="detail.path_full" alt="">
              <div class="img-mask" :class="{show:index === detailIndex}">
                <i class="el-icon-delete" @click="removeDetailImg(index)"></i>
              </div>
            </li>
            <li class="image-item" v-if="isDetailLoading">
              <div class="image-loading" v-loading="true"></div>
            </li>
            <li>
              <div class="upload-area" id="upload-detail-area">
                <label id="upload-detail-trigger" for="upload-detail-files"></label>
                <i class="el-icon-plus"></i>
                <input id="upload-detail-files" type="file">
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import uploader from '@/utils/upload'

  const uploadArr = ['cover', 'detail']

  export default {
    data() {
      return {
        isShowMask: false,
        isDetailLoading: false,
        isCoverLoading: false,
        detailIndex: -1,
        coverIndex: -1,
        coverList: [],
        detailList: [],
      }
    },
    props: {
      coverImage: {
        type: Array,
        'default': () => [],
      },
      detailImage: {
        type: Array,
        'default': () => [],
      },
      isEdit: {
        type: Boolean,
        'default': false,
      },
      productDetail: {
        'default': {},
        type: Object,
      },
      changeType: {
        'default': false,
      },
    },
    mounted() {
      if (this.isEdit) {
        this.coverList = this.coverImage
        this.detailList = this.detailImage
      }
      if (this.changeType) {
        this.coverList = this.productDetail.cover_image
        this.detailList = this.productDetail.detail_image
      }
      // 初始化上传
      uploadArr.forEach(item => {
        const uploadSetting = {
          browse_button: `upload-${item}-trigger`,
          container: `upload-${item}-area`,
          postfiles_button: `upload-${item}-files`,
          path_name: 'goods',
          max_file_size: '3M',
          filesAdded: () => {
            if (item === 'cover') {
              this.isCoverLoading = true
            } else {
              this.isDetailLoading = true
            }

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
            if (item === 'cover') {
              this.isCoverLoading = false
              this.coverList.push(data)
            } else {
              this.isDetailLoading = false
              this.detailList.push(data)
            }
          },

          // 上传失败回调：包括前后端对上传的文件验证不通过、前后端网络异常
          error: msg => {
            this.$message.error(msg)
          },
        }

        uploader.init(uploadSetting)
      })
    },
    methods: {
      showMask(index, tag) {
        if (tag === 'cover') {
          this.coverIndex = index
        } else {
          this.detailIndex = index
        }
      },
      hideMask(index, tag) {
        if (tag === 'cover') {
          this.coverIndex = -1
        } else {
          this.detailIndex = -1
        }
      },
      removeCoverImg(index) {
        this.coverList.splice(index, 1)
      },
      removeDetailImg(index) {
        this.detailList.splice(index, 1)
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import "./goods-images.scss";
</style>
