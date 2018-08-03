<template>
  <div class="page">
    <!--面包屑导航-->
    <el-breadcrumb class="breadcrumb">
      <el-breadcrumb-item>资金管理</el-breadcrumb-item>
      <el-breadcrumb-item>结算单管理</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="funds-content page-content">
      <!--搜索功能-->
      <div class="content-input">
        <ul class="content-input-list">
          <li>
            <span>采购单编号</span>
            <el-input v-model="tradeNo" placeholder="" :clearable=true></el-input>
          </li>
          <li>
            <span>状态</span>
            <el-select v-model="tradeStatus" class="select-status" placeholder="请选择" :clearable=true>
              <el-option
                v-for="item in options"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
          </li>
          <li>
            <span>时间</span>
            <el-date-picker
              v-model="startTime"
              type="date"
              size="small"
              placeholder="选择日期">
            </el-date-picker>
            至
            <el-date-picker
              v-model="overTime"
              type="date"
              size="small"
              placeholder="选择日期">
            </el-date-picker>
          </li>
        </ul>
        <div>
          <span class="search" @click="search">搜索</span>
          <span class="search-all"> 共搜索到{{fundsListTotal}}个采购单</span>
        </div>
      </div>

      <!--table表格-->
      <div class="table">
        <table border="0" cellspacing="0" cellpadding="0">
          <thead class="thead">
            <tr>
              <td style="width: 260px;">供应商</td>
              <td style="width: 145px;">采购单编号</td>
              <td style="width: 150px;">日期</td>
              <td style="width: 130px;">金额</td>
              <td style="width: 100px;">状态</td>
              <td style="width: 135px;">操作</td>
            </tr>
          </thead>
          <tbody class="tbody" v-loading="isLoading" v-if="fundsListData && fundsListData.order_list &&
          fundsListData.order_list.length">
            <tr class="export" v-if="accountType || funds.length > 0">
              <td colspan="6"><span @click="exportFund">导出结算单</span></td>
            </tr>
            <tr v-for="fundItem in fundsListData.order_list">
              <td>
                <div class="supplier_name">{{ fundItem.supplier_name }}</div>
              </td>
              <td>{{ fundItem.purchase_no }}</td>
              <td>{{ fundItem.account_period_time }}</td>
              <td>{{ fundItem.pay_currency }} {{ fundItem.new_pay_price }}</td>
              <td>{{ fundItem.trade_status_text }}</td>
              <td class="view-detail">
                <a href="javascript:void(0);" class="link-active" v-if="accountType || funds.length > 0"
                   @click="toFundsDetail(fundItem.purchase_no)">查看明细</a>
              </td>
            </tr>
          </tbody>
          <tbody v-if="noMore">
            <tr>
              <td colspan="6"><span class="noData">暂无数据</span></td>
            </tr>
          </tbody>
        </table>
        <!--分页功能-->
        <el-pagination
          v-if="fundsListTotal > 10"
          background
          @current-change="handleCurrentChange"
          :current-page.sync="fundPage"
          :page-size="10"
          layout="prev, pager, next, jumper"
          :total="fundsListTotal">
        </el-pagination>
      </div>
    </div>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex'
  import { getFundList } from '@/api/funds/getFunds'
  import { exportExcel } from '@/utils/excel'
  import { param } from '@/utils/param'

  export default {
    name: 'fillManage',
    computed: {
      ...mapGetters(['accountType', 'funds']),
    },
    data() {
      return {
        startTime: '',
        overTime: '',
        isLoading: true,
        fundPage: 1,
        fundsListTotal: 0,
        tradeStatus: '',
        tradeNo: '',
        noMore: false,
        options: [
          {
            value: 5,
            label: '待结算',
          },
          {
            value: 15,
            label: '已完成',
          },
        ],
        value: '',
        fundsListData: {},
      }
    },

    created() {
      this._getFundList()
    },

    methods: {
      formatDate(date) {
        const y = date.getFullYear()
        let m = date.getMonth() + 1

        m = m < 10 ? `0${m}` : m
        let d = date.getDate()

        d = d < 10 ? `0${d}` : d

        return `${y}-${m}-${d} ` + `00:00:00`
      },

      exportFund() {
        let startTime = ''
        let overTime = ''
        let startTimeStamp, overTimeStamp
        const tradeNo = this.tradeNo ? this.tradeNo : ''
        const purchaseStatus = this.tradeStatus ? this.tradeStatus : ''

        if (this.startTime) {
          startTimeStamp = new Date(this.startTime)
          startTime = this.formatDate(startTimeStamp)
        }
        if (this.overTime) {
          overTimeStamp = new Date(this.overTime)
          overTime = this.formatDate(overTimeStamp)
        }
        if (overTimeStamp - startTimeStamp < 0) {
          this.$message.error('结束时间需大于起始时间')
          this.isLoading = false

          return false
        }

        const exportParams = {
          trade_no: tradeNo,
          trade_status: purchaseStatus,
          start_time: startTime,
          over_time: overTime,
          page: this.fundPage,
        }
        const originUrl = `${window.location.origin}/api/funds/fundsList/exportExcel`
        const exportUrl = `${originUrl}${originUrl.indexOf('?') == -1 ? '?' : '&'}${param(exportParams)}`

        exportExcel(exportUrl)
      },
      handleCurrentChange() {
        this._getFundList()
      },

      _getFundList() {
      //        const params = {
      //          page: this.fundPage,
      //        }
        this.isLoading = true
        let startTime = ''
        let overTime = ''
        let startTimeStamp, overTimeStamp
        const tradeNo = this.tradeNo ? this.tradeNo : ''
        const purchaseStatus = this.tradeStatus ? this.tradeStatus : ''

        if (this.startTime) {
          startTimeStamp = new Date(this.startTime)
          startTime = this.formatDate(startTimeStamp)
        }
        if (this.overTime) {
          overTimeStamp = new Date(this.overTime)
          overTime = this.formatDate(overTimeStamp)
        }
        if (overTimeStamp - startTimeStamp < 0) {
          this.$message.error('结束时间需大于起始时间')
          this.isLoading = false

          return false
        }

        const params = {
          trade_no: tradeNo,
          trade_status: purchaseStatus,
          start_time: startTime,
          over_time: overTime,
          page: this.fundPage,
        }

        getFundList(params)
          .then(data => {
            this.isLoading = false
            this.fundsListData = data.data
            this.fundsListTotal = data.data.total
          })
          .catch(error => {
            this.isLoading = false
            this.$message.error(error)
          })
      },
      toFundsDetail(purchaseNo) {
        this.$router.push({
          path: `/funds/${purchaseNo}`,
        })
      },

      search() {
        this.fundPage = 1
        this.isLoading = true
        let startTime = ''
        let overTime = ''
        let startTimeStamp, overTimeStamp
        const tradeNo = this.tradeNo ? this.tradeNo : ''
        const purchaseStatus = this.tradeStatus ? this.tradeStatus : ''

        if (this.startTime) {
          startTimeStamp = new Date(this.startTime)
          startTime = this.formatDate(startTimeStamp)
        }
        if (this.overTime) {
          overTimeStamp = new Date(this.overTime)
          overTime = this.formatDate(overTimeStamp)
        }
        if (overTimeStamp - startTimeStamp < 0) {
          this.$message.error('结束时间需大于起始时间')
          this.isLoading = false

          return false
        }

        const params = {
          trade_no: tradeNo,
          trade_status: purchaseStatus,
          start_time: startTime,
          over_time: overTime,
        }

        getFundList(params)
          .then(data => {
            this.isLoading = false
            this.fundsListData = data.data
            this.fundsListTotal = data.data.total
            this.noMore = false

            if (this.fundsListTotal === 0) {
              this.noMore = true
            }
          })
          .catch(error => {
            this.isLoading = false
            this.$message.error(error)
          })
      },
    },
  }
</script>
<style lang="scss" scoped>
  @import "funds.scss";
</style>
