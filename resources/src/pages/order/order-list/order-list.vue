<template>
  <div class="page">
    <!--面包屑导航-->
    <el-breadcrumb class="breadcrumb">
      <el-breadcrumb-item>订单管理</el-breadcrumb-item>
      <el-breadcrumb-item>我的采购单</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="main-content page-content">
      <!--搜索功能-->
      <div class="content-input">
        <ul class="content-input-list first">
          <li>
            <label>采购单编号</label>
            <el-input v-model="purchaseNo" placeholder="" :clearable=true></el-input>
          </li>
          <li>
            <label>货号</label>
            <el-input v-model="productModel" placeholder="" :clearable=true></el-input>
          </li>
          <li>
            <label>名称</label>
            <el-input v-model="goodsName" placeholder="" :clearable=true></el-input>
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
            <span class="search" @click="search">搜索</span>
            <span class="search-all"> 共搜索到{{orderListTotal}}个采购单</span>
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
              v-model="endTime"
              type="date"
              size="small"
              placeholder="选择日期">
            </el-date-picker>
          </li>
        </ul>
      </div>

      <div class="table">
        <!--tab切换-->
        <div class="tabs">
          <span v-for="(tabItem, index) in tabData" :class="{active: tabIndex == index}"
                @click="tabChange(tabItem.trade_status, index)">{{tabItem.tab_text}}</span>
        </div>
        <div v-loading="isLoading">
          <tab-item :data="orderListData.purchase_trade_list"
                    :purchase_no="purchaseNo"
                    :trade_status="tradeStatus"
                    :start_time="startTime"
                    :end_time="endTime"
                    :logistic_method="logisticMethod"
                    :code="code"
                    :goods_name="goodsName"
                    :product_model="productModel"
                    :check_list="checkList"
                    @reciveSuccess='refreshDom'
                    v-if="orderListData.purchase_trade_list &&
                    orderListData.purchase_trade_list.length > 0">
          </tab-item>
          <div v-else class="noData">
            暂无数据
          </div>
          <!--分页功能-->
          <el-pagination
            v-if="orderListTotal > 10"
            background
            @current-change="handleCurrentChange"
            :current-page.sync="orderPage"
            :page-size="10"
            layout="prev, pager, next, jumper"
            :total="orderListTotal">
          </el-pagination>

        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex'
  import { getOrderList } from '@/api/order'
  import TabItem from './children/tab-item'

  export default {
    name: 'orderList',
    data() {
      return {
        value: '',
        checkList: [],
        orderListData: {},
        tabIndex: 0,
        isLoading: true,
        orderListTotal: 0,
        tradeStatus: '',
        startTime: '',
        endTime: '',
        purchaseNo: '',
        productModel: '',
        goodsName: '',
        code: '',
        logisticMethod: '',
        methodData: [
          {
            value: '1',
            label: '上门取货',
          },
          {
            value: '2',
            label: '送货入仓',
          },
          {
            value: '3',
            label: '物流送货',
          },
        ],
        tabData: [
          {
            tab_text: '所有采购单',
            trade_status: '',
          },
          {
            tab_text: '待确认',
            trade_status: 1,
          },
          {
            tab_text: '待入库',
            trade_status: 4,
          },
          {
            tab_text: '待结算',
            trade_status: 5,
          },
          {
            tab_text: '已完成',
            trade_status: 15,
          },
          {
            tab_text: '已撤销',
            trade_status: 9,
          },
        ],
        paramsData: {},
        orderPage: 1,
        searchData: [
          {
            value: 1,
            label: '待确认',
          },
          {
            value: 4,
            label: '待入库',
          },
          {
            value: 5,
            label: '待结算',
          },
          {
            value: 15,
            label: '已完成',
          },
          {
            value: 9,
            label: '已撤销',
          },
        ],
        activeName: 'allBuyOrders',
      }
    },
    computed: {
      ...mapGetters(['order']),
    },
    mounted() {
      const query = this.$route.query

      if (query.tradeStatus) {
        let activeIndex = 0
        let tradeStatus = parseInt(query.tradeStatus)

        switch (query.tradeStatus) {
          case '1':
            activeIndex = 1
            break
          case '4':
            activeIndex = 2
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
    methods: {
      formatDate(date) {
        const y = date.getFullYear()
        let m = date.getMonth() + 1

        m = m < 10 ? `0${m}` : m
        let d = date.getDate()

        d = d < 10 ? `0${d}` : d

        return `${y}-${m}-${d} 00:00:00`
      },

      refreshDom() {
        this.isLoading = true
        let params = {}
        if (this.tradeStatus === '') {
          params = {}
        } else {
          params = {
            purchase_status: this.tradeStatus,
          }
        }

        getOrderList(params)
          .then(data => {
            this.isLoading = false
            this.orderListData = data.data
            this.orderListTotal = data.data.total
          })
          .catch(error => {
            this.$message.error(error)
          })
      },

      handleCurrentChange() {
        this._getOrderList()
      },

      _getOrderList() {
        this.isLoading = true
        let startTime = ''
        let endTime = ''
        let startTimeStamp, endTimeStamp
        const purchaseNo = this.purchaseNo ? this.purchaseNo : ''
        const purchaseStatus = this.tradeStatus ? this.tradeStatus : ''
        const goodsName = this.goodsName ? this.goodsName : ''
        const productModel = this.productModel ? this.productModel : ''
        const code = this.code ? this.code : ''

        if (this.startTime) {
          startTimeStamp = new Date(this.startTime)
          startTime = this.formatDate(startTimeStamp)
        }
        if (this.endTime) {
          endTimeStamp = new Date(this.endTime)
          endTime = this.formatDate(endTimeStamp)
        }

        if (endTimeStamp - startTimeStamp < 0) {
          this.$message.error('结束时间需大于起始时间')
          this.isLoading = false

          return false
        }
        const params = {
          purchase_no: purchaseNo,
          purchase_status: purchaseStatus,
          start_time: startTime,
          end_time: endTime,
          goods_name: goodsName,
          code,
          product_model: productModel,
          logistic_method: this.logisticMethod,
          page: this.orderPage,
        }

        getOrderList(params)
          .then(data => {
            this.isLoading = false
            this.orderListData = data.data
            this.orderListTotal = data.data.total
          })
          .catch(error => {
            this.$message.error(error)
          })
      },

      tabChange(tradeStatus, index) {
        this.checkList = []
        this.orderPage = 1
        this.tradeStatus = tradeStatus
        this.isLoading = true
        this.tabIndex = index
        let params = {}
        let startTime = ''
        let endTime = ''
        let startTimeStamp, endTimeStamp
        const purchaseNo = this.purchaseNo ? this.purchaseNo : ''
        const goodsName = this.goodsName ? this.goodsName : ''
        const productModel = this.productModel ? this.productModel : ''
        const code = this.code ? this.code : ''

        if (this.startTime) {
          startTimeStamp = new Date(this.startTime)
          startTime = this.formatDate(startTimeStamp)
        }
        if (this.endTime) {
          endTimeStamp = new Date(this.endTime)
          endTime = this.formatDate(endTimeStamp)
        }

        if (endTimeStamp - startTimeStamp < 0) {
          this.$message.error('结束时间需大于起始时间')
          this.isLoading = false

          return false
        }

        params = {
          purchase_no: purchaseNo,
          purchase_status: tradeStatus,
          start_time: startTime,
          end_time: endTime,
          goods_name: goodsName,
          code,
          product_model: productModel,
          logistic_method: this.logisticMethod,
          page: 1,
        }

        getOrderList(params)
          .then(data => {
            this.isLoading = false
            if (data.data.total === 0) {
              this.isLoading = false
            } else {
              data.data.purchase_trade_list.forEach(item => {
                item.is_check = false
              })
            }
            this.orderListData = data.data
            this.orderListTotal = data.data.total
          })
          .catch(error => {
            this.$message.error(error)
          })
      },

      search() {
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
        let endTime = ''
        let startTimeStamp, endTimeStamp
        const purchaseNo = this.purchaseNo ? this.purchaseNo : ''
        const purchaseStatus = this.tradeStatus ? this.tradeStatus : ''
        const goodsName = this.goodsName ? this.goodsName : ''
        const productModel = this.productModel ? this.productModel : ''
        const code = this.code ? this.code : ''

        if (this.startTime) {
          startTimeStamp = new Date(this.startTime)
          startTime = this.formatDate(startTimeStamp)
        }
        if (this.endTime) {
          endTimeStamp = new Date(this.endTime)
          endTime = this.formatDate(endTimeStamp)
        }

        if (endTimeStamp - startTimeStamp < 0) {
          this.$message.error('结束时间需大于起始时间')
          this.isLoading = false

          return false
        }
        const params = {
          purchase_no: purchaseNo,
          purchase_status: purchaseStatus,
          start_time: startTime,
          end_time: endTime,
          goods_name: goodsName,
          code,
          product_model: productModel,
          logistic_method: this.logisticMethod,
        }

        this.$router.push({
          path: '/order/order-list',
          query: {
            purchase_no: purchaseNo,
            purchase_status: purchaseStatus,
            start_time: startTime,
            end_time: endTime,
            goods_name: goodsName,
            code,
            product_model: productModel,
            logistic_method: this.logisticMethod,
          },
        })

        getOrderList(params)
          .then(data => {
            this.isLoading = false
            this.orderListData = data.data
            this.orderListTotal = data.data.total

            if (data.data.total === 0) {
              this.isLoading = false
            }
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
  @import "./order.scss";
</style>
