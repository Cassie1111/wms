<template>
  <div class="page">
    <div class="location">
      <span>分销商管理</span>
      <span>/</span>
      <span>我的分销商</span>
    </div>
    <section class="page-content">
      <div class="page-content-head">
        <div class="title">合作分销商</div>
        <el-button v-if="accountType || retailer.length && retailer[0]['operator_types'].includes(1)"
                   size="mini"
                   type="primary" @click="redirectDetail(0)">添加分销商</el-button>
      </div>
      <el-table v-loading="loading"
                :data="retailerList"
                :header-cell-class-name="tableheaderClassName">
        <el-table-column
          prop="retailer_name"
          label="公司名称"
          width="180"
          align="center">
        </el-table-column>
        <el-table-column
          prop="retailer_no"
          label="供应商编码"
          width="120"
          align="center">
        </el-table-column>
        <el-table-column
          prop="retailer_type_cn"
          label="合作类型"
          align="center">
        </el-table-column>
        <el-table-column
          prop="create_time"
          label="新增时间"
          width="180"
          align="center">
        </el-table-column>
        <el-table-column
          prop="status_cn"
          label="状态"
          align="center">
        </el-table-column>
        <el-table-column
          label="操作"
          align="center">
          <template slot-scope="scope">
            <a href="javascript:void(0);" @click="redirectDetail(scope.row)">查看信息</a>
          </template>
        </el-table-column>
      </el-table>

      <div v-if="total > 20" class="pagination">
        <el-pagination
          background
          @current-change="handleCurrentChange"
          :current-page.sync="page"
          :page-size="20"
          layout="prev, pager, next, jumper"
          :total="total">
        </el-pagination>
      </div>
    </section>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { getList } from '@/api/retailer/retailer'

  export default {
    name: 'retailer-list',
    data() {
      return {
        total: 0,
        retailerList: [],
        page: 1,
        loading: true,
      }
    },
    mounted() {
      this.getRetailerList()
    },
    watch: {
      // 监听路由变化，获取新的列表数据
      $route: 'getRetailerList',
    },
    computed: {
      ...mapGetters(['accountType', 'retailer']),
    },
    methods: {
      tableheaderClassName({ row, rowIndex }) {
        return 'table-head-th'
      },
      getRetailerList() {
        const page = this.$route.query.page || 1

        this.loading = true
        this.page = Number(page)
        const options = {
          page,
        }

        getList(options)
          .then(response => {
            this.retailerList = response.data.retailer_list
            this.total = response.data.total
          })
          .catch(err => {
            // 错误处理
            this.$message.error(err)
          })
          .finally(() => {
            this.loading = false
          })
      },
      handleCurrentChange(page) {
        this.$router.push({
          path: '/retailer',
          query: {
            page,
          },
        })
        this.loading = true
      },
      redirectDetail(row) {
        const retailerNo = row.retailer_no || 0

        this.$router.push({
          path: `/retailer/${retailerNo}`,
        })
      },
    },
  }
</script>

<style scoped lang="scss">
  @import "retailer-list";
</style>

<style lang="scss">
  .el-table {
    thead {
      height: 40px;
      color: #666;
      font-weight: normal;
      .table-head-th {
        background-color: #F2E6CD !important;
      }
    }
    tbody {
      a {
        color: #CDAE63;
      }
    }
  }
</style>