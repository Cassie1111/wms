<template>
  <div class="page">
    <p class="page-title">营销中心 / 模版管理</p>

    <div class="page-content">
      <div class="nav">
        <div class="nav-name">新建轮播</div>
        <div class="nav-btn">
          <el-button size="small" type="primary" @click="save" style="width: 140px;">发布</el-button>
          <router-link to="/banner-list">
            <el-button size="small" class="btn-fff"> 取消 </el-button>
          </router-link>
        </div>
      </div>

      <el-form ref="form" label-position="left" label-width="80px" size="mini" class="form">
        <el-form-item label="活动名称:" >
          <el-input style="width: 30%" v-model="saveData.title"></el-input>
        </el-form-item>
        <el-form-item label="活动时间:">
          <el-col :span="6">
            <el-date-picker type="datetime" placeholder="选择日期" style="width: 100%;"
                            v-model="saveData.start_time" value-format="yyyy-MM-dd HH:mm:ss"></el-date-picker>
          </el-col>
          <el-col class="line" :span="1">-</el-col>
          <el-col :span="6">
            <el-date-picker type="datetime" placeholder="选择日期" style="width: 100%;"
                            v-model="saveData.end_time" value-format="yyyy-MM-dd HH:mm:ss"></el-date-picker>
          </el-col>
        </el-form-item>

        <el-form-item label="封面图:">
          <div class="upload-area" id="upload-area">
            <label id="upload-trigger" for="upload-files">上传照片</label>
            <input id="upload-files" type="file">
            <div class="img-area">
              <img v-if="coverImgPath" :src="coverImgPath"/>
              <img v-else src="../../../../../assets/images/pages/marketing/upload-img.png"/>
            </div>
          </div>
        </el-form-item>
      </el-form>

      <div class="resource-box">
        <p>选择跳转资源</p>
        <div class="resource-form">
          <label>资源类型：</label>
          <el-select style="width: 30%" size="mini" v-model="saveData.resource_type" @change="changeResource">
            <el-option value="1" label="商品集"></el-option>
            <el-option value="2" label="秒杀活动"></el-option>
          </el-select>
        </div>
        <div class="resource-content" v-loading="loading">
          <div class="resource-card" v-for="item in resourceList" @click="selectResource(item)">
            <div class="resource-card-img">
              <img :src="item.cover_image ? item.cover_image.path_full : ''">
              <div class="shade" v-if="item.id == saveData.resource_id">
                <i class="el-icon-check select-icon"></i>
              </div>
            </div>
            <div class="resource-card-desc">
              <p>{{item.title}}</p>
              <p>{{item.create_time}}</p>
            </div>
          </div>

        </div>

        <el-pagination
            v-if="total > submitData.count"
            @current-change="handleCurrentChange"
            class="pagination"
            background
            layout="prev, pager, next, jumper"
            :page-size="submitData.count"
            :current-page="submitData.page"
            :total="total">
        </el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
  import { getBannerDetail, saveBanner } from '@/api/marketing/template'
  import { getResourceList } from '@/api/marketing/resource'
  import { getActivityList } from '@/api/marketing/activity'
  import uploader from '@/utils/upload'

  export default {
    name: 'banner-add',
    data() {
      return {
        loading: true,
        total: 0,
        resourceList: {},
        coverImgPath: '',
        saveData: {
          schedule_id: 0,
          title: '',
          start_time: '',
          end_time: '',
          cover_image: {
            path: '',
            width: '',
            height: '',
          },
          resource_type: '1',
          resource_id: 0,
        },
        submitData: {
          page: 1,
          count: 12,
        },
      }
    },
    mounted() {
      if (this.$route.query.id) {
        this.saveData.schedule_id = this.$route.query.id
        this.getBannerDetail()
      } else {
        this.getCollectionList()
      }

      // 初始化上传
      const uploadSetting = {
        browse_button: 'upload-trigger',
        container: 'upload-area',
        postfiles_button: 'upload-files',
        path_name: 'marketing/banner',
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
          this.coverImgPath = data.path_full
          this.saveData.cover_image.path = data.path
          this.saveData.cover_image.width = data.width
          this.saveData.cover_image.height = data.height
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
    methods: {
      getBannerDetail() {
        getBannerDetail(this.$route.query.id)
          .then(response => {
            const data = response.data.banner_detail

            this.saveData.title = data.title
            this.saveData.start_time = data.start_time
            this.saveData.end_time = data.end_time
            this.saveData.cover_image = data.cover_image
            this.coverImgPath = data.cover_image_path
            this.saveData.resource_type = data.resource_type.toString()
            this.saveData.resource_id = data.resource_id

            this.getCollectionList()
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      getCollectionList() {
        const params = this.submitData

        this.loading = true

        if (this.saveData.resource_type === '1') {
          getResourceList(params)
            .then(response => {
              this.resourceList = response.data.collection_list
              this.total = response.data.total
              this.loading = false
            })
            .catch(err => {
              // 错误处理
              console.log(err)
            })
            .finally(() => {
              // ...
            })
        } else {
          getActivityList(params)
            .then(response => {
              this.resourceList = response.data.activity_list
              this.total = response.data.total
              this.loading = false
            })
            .catch(err => {
              // 错误处理
              console.log(err)
            })
            .finally(() => {
              // ...
            })
        }
      },
      changeResource() {
        this.submitData.page = 1
        this.getCollectionList()
      },
      selectResource(item) {
        this.saveData.resource_id = item.id
      },

      // 分页操作
      handleCurrentChange(val) {
        this.submitData.page = val
        this.getCollectionList()
      },
      save() {
        if (this.saveData.start_time && this.saveData.end_time) {
          const startTime = new Date(this.saveData.start_time).getTime()
          const endTime = new Date(this.saveData.end_time).getTime()

          if (startTime > endTime) {
            this.$message({
              message: '活动结束时间不能小于活动开始时间',
              type: 'warning',
            })

            return
          }
        }

        if (!this.saveData.resource_id) {
          this.$message({
            message: '请添加跳转资源',
            type: 'error',
          })

          return
        }

        saveBanner(this.saveData)
          .then(response => {
            this.$message({
              message: '保存成功',
              type: 'success',
            })

            setTimeout(() => {
              this.$router.push({
                name: 'banner-list',
              })
            }, 2000)
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
    },
  }
</script>

<style lang="scss" scoped>
  .page-title,
  .page-content {
    width: 100%;
    background-color: #ffffff;
  }

  .page-title {
    padding-left: 20px;
    margin-bottom: 10px;
    line-height: 40px;
  }

  .nav {
    display: flex;
    padding-top: 30px;
  }

  .btn-fff{
    width: 140px;
    color: #999999;
    margin-left: 20px;
  }

  .nav-name {
    width: 150px;
    text-align: center;
    line-height: 40px;
    border: 2px solid #CDAE63;
    border-bottom: none;
    color: #CDAE63;
  }

  .nav-btn {
    border-bottom: 2px solid #CDAE63;
    padding-left: 480px;
  }

  .form {
    padding: 20px;
  }

  .line {
    text-align: center;
  }

  .upload-area {
    position: relative;
    label {
      position: absolute;
      opacity: 0;
      width: 300px;
      height: 150px;
    }
    input {
      display: none;
    }
    .img-area {
      img {
        width: 300px;
        height: 150px;
      }
    }
  }

  .resource-box {
    padding: 20px;
  }

  .resource-form {
    border-top: 1px solid #cacaca;
    margin-top: 19px;
    padding: 20px 0;
  }

  .resource-content {
    display: flex;
    flex-wrap: wrap;
  }

  .resource-card {
    width: 276px;
    height: 214px;
    margin-right: 20px;
    margin-bottom: 20px;
  }

  .resource-card-img {
    height: 150px;
    width: 100%;
    position: relative;
    border: 1px solid #cacaca;
  }

  .resource-card-img img {
    width: 100%;
    height: 100%;
  }

  .shade {
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    position: absolute;
    top: 0;
  }

  .select-icon {
    color: #85FE00;
    font-size: 50px;
    font-weight: 600;
    margin: 50px 115px;
  }

  .resource-card-desc {
    width: 100%;
    height: 64px;
    border: 1px solid #cacaca;
    border-top: none;
  }

  .resource-card-desc p {
    padding: 5px;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    &:nth-child(2) {
      font-size: 12px;
      color: #666666;
    }
  }

  .pagination {
    text-align: center;
    margin-top: 20px;
  }
</style>