<template>
  <div class="page">
    <!--面包屑导航-->
    <el-breadcrumb class="breadcrumb">
      <el-breadcrumb-item>订单管理</el-breadcrumb-item>
      <el-breadcrumb-item>我的订单</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="main-content page-content">
      <!--搜索功能-->
      <div class="content-input">
        <ul class="content-input-list first">
          <li>
            <label>订单编号</label>
            <el-input v-model="purchaseNo" placeholder="" :clearable=true></el-input>
          </li>
          <li>
            <label>货号</label>
            <el-input v-model="brandNo" placeholder="" :clearable=true></el-input>
          </li>
          <li>
            <label>名称</label>
            <el-input v-model="commodityName" placeholder="" :clearable=true></el-input>
          </li>
        </ul>
        <ul class="content-input-list second">
          <li>
            <label>状态</label>
            <el-select v-model="tradeStatus" class="select-status" :clearable=true>
              <el-option
                v-for="item in searchData"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
          </li>
          <li>
            <label>编码</label>
            <el-input v-model="code" placeholder="" :clearable=true></el-input>
          </li>
          <li>
            <span class="search" @click="getSearch">搜索</span>
            <span class="search-all"> 共搜索到{{orderListData.total}}个订单</span>
          </li>
        </ul>
        <ul class="content-input-list third">
          <li>
            <label>配送方式</label>
            <el-select v-model="logisticMethod" class="select-status" size="small" :clearable=true>
              <el-option
                v-for="item in methodData"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
          </li>
          <li>
            <label>时间</label>
            <el-date-picker
              v-model="startTime"
              type="date"
              size="small"
              placeholder="选择日期">
            </el-date-picker> 至
            <el-date-picker
              v-model="overTime"
              type="date"
              size="small"
              placeholder="选择日期">
            </el-date-picker>
          </li>
        </ul>
      </div>

      <div class="table">
        <div class="tabs">
          <span v-for="(tabItem, index) in tabData" :class="{active: tabIndex == index}"
                @click="tabChange(tabItem.trade_status, index)">{{tabItem.tab_text}}</span>
        </div>
        <div v-loading="isLoading">
          <tab-item :data="orderListData.retail_trade_list"
                    :start_time="startTime"
                    :over_time="overTime"
                    :purchase_no="purchaseNo"
                    :trade_status="tradeStatus"
                    :logistic_method="logisticMethod"
                    :code="code"
                    :goods_name="commodityName"
                    :brand_no="brandNo"
                    :checked_list="checkedList"
                    @outOrder="refreshDom"
                    @successChange="getSearch"
                    v-if="orderListData.retail_trade_list && orderListData.retail_trade_list.length > 0"></tab-item>
          <div v-else class="noData">
            暂无数据
          </div>
          <!--分页功能-->
          <el-pagination
            v-if="orderListData.total && orderListData.total > 10"
            background
            @current-change="handleCurrentChange"
            :current-page.sync="orderPage"
            :page-size="10"
            layout="prev, pager, next, jumper"
            :total="orderListData.total">
          </el-pagination>

        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex'
  import { getSettingOrderList } from '@/api/order'
  import TabItem from './children/tab-item'

  export default {
    name: 'orderList',
    data() {
      return {
        value: '',
        orderListData: {},
        orderPage: 1,
        isLoading: true,
        checkedList: [],
        tabIndex: 0,
        trideNo: '',
        orderListTotal: 0,
        tradeStatus: '',
        startTime: '',
        overTime: '',
        purchaseNo: '',
        supplierNo: '',
        brandNo: '',
        commodityName: '',
        logisticMethod: '',
        code: '',
        methodData: [
          {
            value: 1,
            label: '自提',
          },
          {
            value: 2,
            label: '单个地址发货',
          },
          {
            value: 3,
            label: '多个地址发货',
          },
        ],
        tabData: [
          {
            tab_text: '全部订单',
            trade_status: '',
          },
          {
            tab_text: '待付款',
            trade_status: 2,
          },
          {
            tab_text: '待发货',
            trade_status: 3,
          },
          {
            tab_text: '待签收',
            trade_status: 4,
          },
          {
            tab_text: '交易成功',
            trade_status: 5,
          },
          {
            tab_text: '订单关闭',
            trade_status: 9,
          },
        ],
        paramsData: {},
        searchData: [
          {
            value: 2,
            label: '待付款',
          },
          {
            value: 3,
            label: '待发货',
          },
          {
            value: 4,
            label: '待签收',
          },
          {
            value: 5,
            label: '交易成功',
          },
          {
            value: 9,
            label: '订单关闭',
          },
        ],
      }
    },
    mounted() {
      const query = this.$route.query

      if (query && query.tradeStatus) {
        let activeIndex = 0
        const tradeStatus = parseInt(query.tradeStatus)

        switch (query.tradeStatus) {
          case '3':
            activeIndex = 2
            break
          case '4':
            activeIndex = 3
            break
          case '5':
            activeIndex = 3
            break
          default:
            break
        }

        this.tabChange(tradeStatus, activeIndex)
      } else {
        this._getOrderList()
      }
    },

    computed: {
      ...mapGetters(['accountType', 'settingOrder']),
    },

    methods: {
      formatDate(date) {
        const y = date.getFullYear()
        let m = date.getMonth() + 1

        m = m < 10 ? `0${m}` : m
        let d = date.getDate()

        d = d < 10 ? `0${d}` : d

        return `${y}-${m}-${d} 00:00:00`
      },

      handleCurrentChange() {
        this._getOrderList()
      },

      _getOrderList() {
        this.isLoading = true
        let startTime = ''
        let overTime = ''
        let params = {}
        let startTimeStamp, overTimeStamp
        const purchaseNo = this.purchaseNo ? this.purchaseNo : ''
        const purchaseStatus = this.tradeStatus ? this.tradeStatus : ''
        const commodityName = this.commodityName ? this.commodityName : ''
        const brandNo = this.brandNo ? this.brandNo : ''
        const code = this.code ? this.code : ''
        const logisticMethod = this.logisticMethod ? this.logisticMethod : ''

        startTime = this.startTime ? this.startTime : ''
        overTime = this.overTime ? this.overTime : ''

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

        if (purchaseStatus === '' && commodityName === '' && brandNo === '' && code === '' && logisticMethod === '' &&
          startTime === '' && overTime === '') {
          params = {
            trade_no: purchaseNo,
            page: this.orderPage,
            is_search: 0,
          }
        } else {
          params = {
            trade_no: purchaseNo,
            trade_status: purchaseStatus,
            product_model: brandNo,
            start_time: startTime,
            end_time: overTime,
            goods_name: commodityName,
            code,
            is_search: 1,
            page: this.orderPage,
            logistic_method: logisticMethod,
          }
        }

        getSettingOrderList(params)
          .then(data => {
            this.isLoading = false
            this.orderListData = data.data
          })
          .catch(error => {
            this.$message.error(error)
          })
      },

      refreshDom() {
        this.isLoading = true
        let params = {}

        if (this.tradeStatus === '') {
          params = {}
        } else {
          params = {
            trade_status: this.tradeStatus,
            is_search: 1,
          }
        }

        getSettingOrderList(params)
          .then(data => {
            this.isLoading = false
            this.orderListData = data.data
          })
          .catch(error => {
            this.$message.error(error)
          })
      },

      tabChange(tradeStatus, index) {
        this.orderPage = 1
        this.checked_list = []
        this.tradeStatus = tradeStatus
        this.isLoading = true
        this.tabIndex = index
        let params = {}
        let startTime = ''
        let overTime = ''
        let startTimeStamp, overTimeStamp
        const purchaseNo = this.purchaseNo ? this.purchaseNo : ''
        const commodityName = this.commodityName ? this.commodityName : ''
        const brandNo = this.brandNo ? this.brandNo : ''
        const code = this.code ? this.code : ''
        const logisticMethod = this.logisticMethod ? this.logisticMethod : ''

        startTime = this.startTime ? this.startTime : ''
        overTime = this.overTime ? this.overTime : ''

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

        if (index === 0) {
          params = {
            page: 1,
          }
        } else {
          params = {
            trade_no: purchaseNo,
            trade_status: tradeStatus,
            page: 1,
            product_model: brandNo,
            start_time: startTime,
            end_time: overTime,
            goods_name: commodityName,
            code,
            is_search: 1,
            logistic_method: logisticMethod,
          }
        }

        getSettingOrderList(params)
          .then(data => {
            this.isLoading = false
            if (data.data.total === 0) {
              this.isLoading = false
            } else {
              data.data.retail_trade_list.forEach(searchItem => {
                searchItem.is_check = false
              })
            }

            this.orderListData = data.data
            this.orderListTotal = data.data.total
          })
          .catch(error => {
            this.$message.error(error)
          })
      },

      getSearch() {
        this.orderPage = 1
        if (this.tradeStatus === '') {
          this.tabIndex = 0
        } else {
          this.searchData.forEach((searchItem, index) => {
            if (this.tradeStatus === searchItem.value) {
              this.tabIndex = index + 1
            }
          })
        }

        this.isLoading = true
        let startTime = ''
        let overTime = ''
        let params = {}
        let startTimeStamp, overTimeStamp
        const purchaseNo = this.purchaseNo ? this.purchaseNo : ''
        const purchaseStatus = this.tradeStatus ? this.tradeStatus : ''
        const commodityName = this.commodityName ? this.commodityName : ''
        const brandNo = this.brandNo ? this.brandNo : ''
        const code = this.code ? this.code : ''
        const logisticMethod = this.logisticMethod ? this.logisticMethod : ''

        startTime = this.startTime ? this.startTime : ''
        overTime = this.overTime ? this.overTime : ''

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

        if (purchaseStatus === '' && commodityName === '' && brandNo === '' && code === '' && logisticMethod === '' &&
          startTime === '' && overTime === '') {
          params = {
            trade_no: purchaseNo,
            is_search: 0,
          }
        } else {
          params = {
            trade_no: purchaseNo,
            trade_status: purchaseStatus,
            product_model: brandNo,
            start_time: startTime,
            end_time: overTime,
            goods_name: commodityName,
            code,
            is_search: 1,
            logistic_method: logisticMethod,
          }
        }

        getSettingOrderList(params)
          .then(data => {
            this.isLoading = false
            this.orderListData = data.data
            this.orderListTotal = data.data.total
          })
          .catch(error => {
            this.isLoading = false
            this.$message.error(error)
          })
      },
    },
    components: {
      TabItem,
    },
  }
</script>
<style>
  .table .el-tabs .el-tabs__item{
    font-size: 12px;
  }
</style>
<style lang="scss" scoped>
  @import "./setting-order-list.scss";
</style>
