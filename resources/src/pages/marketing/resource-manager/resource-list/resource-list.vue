<template>
  <div class="page">
    <p class="title">营销中心 / 资源管理</p>

    <div class="page-content">
      <div class="nav">
        <p>商品集</p>
        <el-button size="mini" type="primary" @click="linkToAdd"
                   v-if="accountType === 1 || resource[0]['operator_types'].includes(1)">新建商品集</el-button>
      </div>
      <div class="resource-content" v-loading="loading">
        <div class="resource-card" v-for="item in resourceList.collection_list">
          <div class="resource-card-img">
            <img :src="item.cover_image.path_full">
            <div class="shade">
              <p><el-button size="mini" type="primary" @click="modifyResource(item.id)"
                            v-if="accountType === 1 || resource[0]['operator_types'].includes(1)">编辑</el-button></p>
              <p><el-button size="mini" @click="deleteResource(item.id)"
                            v-if="accountType === 1 || resource[0]['operator_types'].includes(1)">删除</el-button></p>
            </div>
          </div>
          <div class="resource-card-desc">
            <p>{{item.title}}</p>
            <p>{{item.create_time}}</p>
          </div>
        </div>

      </div>

      <el-pagination
          v-if="resourceList.total > pageData.count"
          @current-change="handleCurrentChange"
          class="pagination"
          background
          layout="prev, pager, next, jumper"
          :page-size="pageData.count"
          :current-page="pageData.page"
          :total="resourceList.total">
      </el-pagination>
    </div>

  </div>
</template>

<script>
  import { getResourceList, deleteResource } from '@/api/marketing/resource'
  import { mapGetters } from 'vuex'

  export default {
    name: 'resource-list',
    data() {
      return {
        loading: true,
        resourceList: [],
        pageData: {
          page: 1,
          count: 12,
        },
      }
    },
    computed: {
      ...mapGetters(['accountType', 'resource']),
    },
    mounted() {
      this.getResourceList()
    },
    methods: {
      getResourceList() {
        this.loading = true

        getResourceList(this.pageData)
          .then(response => {
            this.resourceList = response.data
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
      deleteResource(id) {
        this.$confirm('是否删除此商品集?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
        }).then(() => {
          deleteResource(id)
            .then(response => {
              this.$message({
                message: '删除成功',
                type: 'success',
              })
              setTimeout(() => {
                this.getResourceList()
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
      modifyResource(id) {
        this.$router.push({
          name: 'resource-add',
          query: {
            id,
          },
        })
      },
      linkToAdd() {
        this.$router.push({
          name: 'resource-add',
        })
      },
      handleCurrentChange(val) {
        // 分页操作
        this.pageData.page = val
        this.getResourceList()
      },
    },
  }
</script>

<style lang="scss" scoped>
  .title,
  .page-content {
    width: 100%;
    background-color: #ffffff;
  }

  .title {
    line-height: 40px;
    padding-left: 20px;
  }

  .page-content {
    margin-top: 10px;
  }
  .nav{
    display: flex;
    justify-content: space-between;
    padding: 25px 55px 25px 40px;
  }

  .resource-content {
    display: flex;
    flex-wrap: wrap;
    padding: 0 40px;
  }

  .resource-card {
    width: 276px;
    height: 214px;
    margin-right: 14px;
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

  .resource-card-img:hover .shade{
    display: block;
  }

  .shade {
    display: none;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    position: absolute;
    top: 0;
    button {
      width: 90px;
    }
  }

  .shade p {
    text-align: center;
    &:nth-child(1) {
      padding-top: 37px;
    }
    &:nth-child(2) {
      padding-top: 20px;
    }
  }
  .resource-card-desc {
    width: 100%;
    height: 64px;
    background-color: #F8F8F8;
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

  .pagination{
    text-align: center;
    margin: 40px 0;
  }
</style>