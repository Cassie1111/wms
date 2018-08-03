<template>
  <div class="page">
    <div class="location">
      <span>供应商管理</span>
      <span>／</span>
      <span>我的供应商</span>
    </div>
    <section class="page-content">
      <div class="title">合作供应商</div>
      <div class="supplier-form-area">
        <el-table v-loading="loading"
                  :data="supplierList"
                  :header-cell-class-name="tableheaderClassName">
          <el-table-column
            prop="supplier_name"
            label="公司名称"
            width="180"
            align="center">
          </el-table-column>
          <el-table-column
            prop="supplier_no"
            label="供应商编码"
            width="120"
            align="center">
          </el-table-column>
          <el-table-column
            prop="cooperate_type_cn"
            label="合作类型"
            align="center">
          </el-table-column>
          <el-table-column
            prop="create_time"
            label="创建时间"
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
              <a href="javascript:void(0);" @click="viewDetail(scope.row)">查看资质</a>
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
      </div>
    </section>
  </div>
</template>
<script>
  import { getList } from '@/api/supplier/supplier'

  export default {
    name: 'my-supplier',
    data() {
      return {
        total: 0,
        page: 1,
        loading: true,
        supplierList: [],
      }
    },
    mounted() {
      this.getSupplierList()
    },
    watch: {
      // 监听路由变化，获取新的列表数据
      $route: 'getSupplierList',
    },
    methods: {
      tableheaderClassName({ row, rowIndex }) {
        return 'supplier-table-head-th'
      },
      handleCurrentChange(page) {
        this.$router.push({
          path: '/supplier',
          query: {
            page,
          },
        })
        this.loading = true
      },
      getSupplierList() {
        const page = this.$route.query.page || 1

        this.loading = true
        this.page = Number(page)
        const options = {
          page,
        }

        getList(options)
          .then(response => {
            this.supplierList = response.data.supplier_list
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
      viewDetail(row) {
        const supplierNo = row.supplier_no

        this.$router.push({
          path: `/supplier/${supplierNo}`,
        })
      },
    },
  }
</script>
<style scoped lang="scss">
  .page-content {
    padding: 0 40px;
    background-color: #fff;
    min-height: 940px;
    .title {
      padding: 25px 0 20px;
      font-size: 20px;
      color: #CDAE63;
    }
  }
</style>
<style lang="scss">
  .supplier-form-area {
    .el-table {
      thead {
        height: 40px;
        color: #666;
        font-weight: normal;
        .supplier-table-head-th {
          background-color: #F2E6CD !important;
        }
      }
      tbody {
        a {
          color: #CDAE63;
        }
      }
    }
    .pagination {
      padding: 20px 0;
      text-align: center;
    }
  }
</style>