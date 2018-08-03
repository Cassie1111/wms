<template>
  <div class="page">
    <p class="page-title">营销中心 / 模版管理</p>

    <div class="page-content">
      <div class="nav">
        <div class="nav-name show">
          <router-link to="/banner-list">
            轮播配置
          </router-link>
        </div>
        <div class="nav-name">
          <router-link :to="{name: 'activity-config'}" v-if="accountType === 1 || activityConfig.length">
            活动配置
          </router-link>
        </div>
        <div class="nav-btn">
          <el-button type="primary" @click="linkToAdd" size="small" style="width: 140px;"
                     v-if="accountType === 1 || banner[0]['operator_types'].includes(1)">新建轮播</el-button>
        </div>
      </div>
      <div class="table">
        <el-table :data="tableData" header-cell-class-name="table-thead" row-class-name="table-tr"
                  size="small" v-loading="loading">
          <el-table-column type="index" label="序号" width="100" align="center"></el-table-column>
          <el-table-column prop="cover_image" label="轮播图片" width="180" align="center">
            <template slot-scope="scope">
              <div class="img-box">
                <img :src="scope.row.cover_image ? scope.row.cover_image.path_full : ''" alt="">
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="title" label="活动名称" width="150" align="center"></el-table-column>
          <el-table-column label="生效时间" width="140" align="center">
            <template slot-scope="scope">
              <span>{{scope.row.start_time}}</span><br>
              <span>至</span><br>
              <span>{{scope.row.end_time}}</span><br>
            </template>
          </el-table-column>
          <el-table-column prop="update_time" label="更新时间" width="140" align="center"></el-table-column>
          <el-table-column prop="username" label="操作人" width="140" align="center"></el-table-column>
          <el-table-column label="操作" width="100" align="center">
            <template slot-scope="scope">
              <el-button type="text" size="small" @click="editBanner(scope.row.id)"
                         v-if="accountType === 1 || banner[0]['operator_types'].includes(1)">编辑
              </el-button>
              <br>
              <el-button type="text" size="small" class="delete-btn" @click="deleteBanner(scope.row.id)"
                         v-if="accountType === 1 || banner[0]['operator_types'].includes(1)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
  </div>
</template>

<script>
  import { getBannerList, deleteBanner } from '@/api/marketing/template'
  import { mapGetters } from 'vuex'

  export default {
    name: 'banner-list',
    data() {
      return {
        loading: true,
        tableData: [],
      }
    },
    computed: {
      ...mapGetters([
        'accountType',
        'banner',
        'activityConfig',
      ]),
    },
    created() {
      this.fetchData()
    },
    methods: {
      fetchData() {
        this.loading = true
        getBannerList()
          .then(response => {
            this.tableData = response.data.banner_list
            this.loading = false
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      deleteBanner(id) {
        this.$confirm('是否删除此轮播?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
        }).then(() => {
          deleteBanner(id)
            .then(response => {
              this.$message({
                message: '删除成功',
                type: 'success',
              })

              setTimeout(() => {
                this.fetchData()
              }, 2000)
            })
            .catch(err => {
              // 错误处理
              console.log(err)
            })
            .finally(() => {
              // ...
            })
        }).catch(() => {
          // ...
        })
      },
      editBanner(id) {
        this.$router.push({
          name: 'banner-add',
          query: {
            id,
          },
        })
      },
      linkToAdd() {
        if (this.tableData.length > 5) {
          this.$message({
            message: '最多可存在6个轮播，请通过编辑替换轮播内容',
            type: 'error',
          })
        } else {
          this.$router.push({
            name: 'banner-add',
          })
        }
      },
    },
  }
</script>

<style lang="scss" scoped>
  a:hover {
    text-decoration: none;
  }

  .nav {
    height: 70px;
    display: flex;
    padding-top: 30px;
    background-color: #ffffff;
  }

  .nav .nav-name {
    width: 150px;
    text-align: center;
    line-height: 40px;
    border-bottom: 2px solid #CDAE63;
    a {
      color: #666666;
    }
  }

  .nav .nav-btn {
    width: 650px;
    border-bottom: 2px solid #CDAE63;
    padding-left: 500px;
  }

  .nav .show {
    border: 2px solid #CDAE63;
    border-bottom: none;
    a {
      color: #CDAE63;
    }
  }

  .delete-btn {
    color: #D0021B;
  }

  .img-box {
    margin: 20px 0px;
    width: 156px;
    height: 78px;
    img {
      width: 100%;
      height: 100%;
    }
  }

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

  .table-thead th {
    padding: 8px 0 !important;
  }


</style>