<template>
  <div class="page">
    <div class="location">
      <span>设置</span>
      <span> / </span>
      <span>子账号管理</span>
    </div>

    <div class="page-content">
    <div class="header">
      <span class="com-name">供应公司名称</span>
      <!--<el-input v-model="username" placeholder="请输入员工账号名">-->
        <!--<i slot="suffix" class="el-input__icon el-icon-search" @click="search"></i>-->
      <!--</el-input>-->
    </div>

    <div class="middle">
      <div class="account-stat">
        <div class="account-stat-header">账号概况</div>
        <div class="account-stat-body">
          <div class="account-stat-item">
            <span class="label">可用数量</span>
            <span class="num">{{total}}</span>
          </div>
          <div class="account-stat-item">
            <span class="label">使用中</span>
            <span class="num">{{enableNum}}</span>
          </div>
          <div class="account-stat-item">
            <span class="label">可新建</span>
            <span class="num">{{disableNum}}</span>
          </div>
        </div>
      </div>
      <div class="btn-group">
        <div class="new-account-btn" v-if="accountType || account.length > 0 && account[0].operator_types.length > 0"
             @click="addAccount">
          <img src="../../../../assets/images/pages/settings/add-icon.png">
          新建员工
        </div>
      </div>
    </div>

    <div class="account-list" v-loading="loading">
      <el-table :data="tableData">
        <el-table-column
          prop="duty"
          label="岗位名称"
          align="center">
        </el-table-column>
        <el-table-column
          prop="realname"
          label="员工姓名"
          align="center">
        </el-table-column>
        <el-table-column
          prop="mobile_phone"
          label="绑定手机"
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
          <template slot-scope="scope" v-if="accountType || account.length > 0 && account[0].operator_types.length > 0">
            <a class="operate-link" href="javascript:void(0);" @click="editAccount(scope.row)">编辑</a>
            <a class="operate-link" href="javascript:void(0);" @click="delAccount(scope.row)">删除</a>
          </template>
        </el-table-column>
      </el-table>
    </div>
  </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { Message } from 'element-ui'
  import {
    getAccountList,
    deleteAccount,
  } from '@/api/settings/account-list'

  export default {
    name: 'account-list',
    data() {
      return {
        total: 5,
        disableNum: 0,
        enableNum: 0,
        username: '',
        tableData: [],
        loading: true,
      }
    },
    mounted() {
      this.getTableData()
    },
    methods: {
      // 获取员工列表
      getTableData() {
        getAccountList().then(res => {
          this.loading = false
          this.tableData = res.data.user_list
          this.total = res.data.total
          this.disableNum = res.data.disable_num < 0 ? 0 : res.data.disable_num
          this.enableNum = res.data.enable_num
        }).catch(() => {
          this.loading = false
        })
      },

      addAccount() {
        if (this.tableData.length >= 50) {
          Message({
            message: '目前最多可创建50个子账号',
            type: 'error',
          })
        } else {
          this.$router.push({
            name: 'add-account',
          })
        }
      },

      // 编辑账号
      editAccount(row) {
        this.$router.push({
          name: 'edit-account',
          params: {
            userNo: row.user_no,
          },
        })
      },

      // 删除账号
      delAccount(row) {
        const userNo = row.user_no

        deleteAccount(userNo).then(() => {
          this.getTableData()

          Message({
            message: '删除成功',
            type: 'success',
          })
        })
      },
    },
    computed: {
      ...mapGetters(['accountType', 'account']),
      accountNum() {
        const temp = {
          available: 5,
          inUse: 0,
          creatable: 0,
        }

        if (this.tableData.length < 1) return temp

        this.tableData.forEach(item => {
          if (item.status === 1) {
            temp.inUse += 1
          }
        })

        temp.creatable = temp.available - temp.inUse < 0 ? 0 : temp.available - temp.inUse

        return temp
      },
      creatable() {
        if (this.accountNum.creatable > 0) return true

        return false
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import 'account-list';
</style>

<style lang="scss">
  $theme-color: #cdae63;

  .el-table {
    thead {
      color: #666;
      font-weight: normal;

      th {
        height: 40px !important;
        padding: 0;
        line-height: 40px;
        border-top: 1px solid #d1d1d1 !important;
        border-bottom: 1px solid #d1d1d1 !important;
      }
    }

    tbody {
      td {
        height: 50px !important;
        line-height: 50px;
        border-bottom: 1px dashed #e9e9e9 !important;
      }
    }
  }
</style>