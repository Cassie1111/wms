<template>
  <div class="page">
    <div class="page-content">
      <div class="supplier-info">
        <div class="supplier-info-cube supplier-profile">
          <div class="cube-icon">
            <img src="../../assets/images/pages/home/icon1.png">
          </div>
          <div class="cube-content">
            <div class="com-name">{{mainAccountInfo.username}}</div>
            <div class="com-email">{{mainAccountInfo.email}}</div>
            <div class="icon-group">
            <span class="icon icon3">
              <img src="../../assets/images/pages/home/icon3.png">
            </span>
              <span class="icon icon4">
              <img src="../../assets/images/pages/home/icon4.png">
            </span>
              <span class="icon">|</span>
              <span class="icon icon5">
              <img src="../../assets/images/pages/home/icon5.png">
            </span>
            </div>
          </div>
        </div>
        <div class="supplier-info-cube finance-info">
          <div class="cube-icon">
            <img src="../../assets/images/pages/home/icon2.png">
          </div>
          <div class="cube-content">
            <div class="wait-pay">
              <label>待结算:</label>
              <span>HK$ {{unPayMoney | numberFormat(2)}}</span>
            </div>
            <div class="deposit">
              <label>保证金</label>
              <span>HK$ 0.00</span>
            </div>
            <div class="jump">
              <span>
                <router-link :to="{name: 'funds'}">结算中心</router-link>
              </span>
              <!--<span class="separator">|</span>-->
              <!--<span>-->
                <!--<router-link to="/jiesuanzhongxin">查看流水</router-link>-->
              <!--</span>-->
            </div>
          </div>
        </div>
      </div>
      <div class="bg-manage">
        <div class="bg-manage-header">后台管理</div>
        <div class="bg-manage-body">
          <label>订单管理</label>
          <div class="line">
            <router-link to="/order/setting-order-list?tradeStatus=3">
              <div class="line-item">
                <img src="../../assets/images/pages/home/icon6.png">
                <span>待发货</span>
                <span class="num">{{ processingCount }}</span>
              </div>
            </router-link>

            <router-link to="/order/setting-order-list?tradeStatus=4">
              <div class="line-item">
                <img src="../../assets/images/pages/home/icon7.png">
                <span>待签收</span>
                <span class="num">{{shippedCount}}</span>
              </div>
            </router-link>

            <router-link to="/order/setting-order-list?tradeStatus=4">
              <div class="line-item">
                <img src="../../assets/images/pages/home/icon8.png">
                <span>待提货</span>
                <span class="num">{{stayGoodsCount}}</span>
              </div>
            </router-link>
          </div>

          <label>采购单管理</label>
          <div class="line">
            <router-link to="/order/order-list?tradeStatus=1">
              <div class="line-item">
                <img src="../../assets/images/pages/home/icon9.png">
                <span>待确认</span>
                <span class="num">{{ pendingCount }}</span>
              </div>
            </router-link>

            <router-link to="/order/order-list?tradeStatus=4">
              <div class="line-item">
                <img src="../../assets/images/pages/home/icon10.png" style="width: 22px;">
                <span>待入库</span>
                <span class="num">{{stayStorageCount}}</span>
              </div>
            </router-link>

            <router-link to="/order/order-list?tradeStatus=5">
              <div class="line-item">
                <img src="../../assets/images/pages/home/icon11.png">
                <span>已入库</span>
                <span class="num">{{storageCount}}</span>
              </div>
            </router-link>
          </div>

          <label>货品管理</label>
          <div class="line">
            <router-link to="/goods/selling-goods">
              <div class="line-item">
                <img src="../../assets/images/pages/home/icon12.png">
                <span>分销中的商品</span>
                <span class="num">{{sellGoodsCount}}</span>
              </div>
            </router-link>


            <router-link to="/goods/warehouse?status=-10">
              <div class="line-item">
                <img src="../../assets/images/pages/home/icon13.png">
                <span>缺货商品</span>
                <span class="num">{{stockOutGoodsCount}}</span>
              </div>
            </router-link>
          </div>
        </div>
      </div>
      <div class="pay-list" v-loading="loading">
        <div class="pay-list-header">结算</div>
        <div class="pay-list-body">
          <el-table class="table" :data="orderList">
            <el-table-column
              prop="purchase_no"
              label="采购单编号"
              align="center">
            </el-table-column>
            <el-table-column
              prop="create_time"
              label="下单时间"
              align="center"
              width="150px">
            </el-table-column>
            <el-table-column
              prop="price"
              label="金额"
              align="center">
            </el-table-column>
            <el-table-column
              prop="account_period_time"
              label="账期时间"
              align="center"
              width="150px">
            </el-table-column>
            <el-table-column
              prop="status"
              label="状态"
              align="center"
              width="100px">
            </el-table-column>
            <el-table-column
              label="操作"
              align="center"
              width="100px">
              <template slot-scope="scope" v-if="accountType || funds.length > 0">
                <a class="operate-link" href="javascript:void(0);" @click="checkDetail(scope.row)">查看明细</a>
              </template>
            </el-table-column>
          </el-table>

          <el-pagination
            class="pagination"
            v-if="orderListTotal > 10"
            :page-size="10"
            :current-page.sync="currentPage"
            @current-change="handleCurrentChange"
            background
            layout="prev, pager, next, jumper"
            :total="orderListTotal">
          </el-pagination>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import {
    index,
    getBgManageCount,
    getMainAccountInfo,
  } from '../../api/home/home'

  export default {
    data() {
      return {
        mainAccountInfo: {},

        /**
         * 待发货：processingCount
         * 待签收：shippedCount
         * 待提货：stayGoodsCount
         * 待入库：stayStorageCount
         * 已入库：storageCount
         * 待确认：pendingCount
         * 分销中的商品：sellGoodsCount
         * 缺货商品：stockOutGoodsCount
         */
        processingCount: '',
        shippedCount: '',
        stayGoodsCount: '',
        stayStorageCount: '',
        storageCount: '',
        pendingCount: '',
        sellGoodsCount: '',
        stockOutGoodsCount: '',
        orderList: [],
        orderListTotal: 0,
        loading: true,
        currentPage: 0,
        unPayMoney: '',
      }
    },

    mounted() {
      this.setMainAccountInfo()
      this.setBgManageCount()

      const page = Number(this.$route.query.page) || 1

      index(page).then(res => {
        this.loading = false
        this.setOrderList(res.data)
        this.setUnPayMoney(res.data)
      })

      this.currentPage = page
    },

    methods: {
      setMainAccountInfo() {
        getMainAccountInfo().then(res => {
          this.mainAccountInfo = res.data.user_detail
        })
      },

      setBgManageCount() {
        getBgManageCount().then(res => {
          const data = res.data

          this.processingCount = data.processing_count
          this.shippedCount = data.shipped_count
          this.stayGoodsCount = data.stay_goods_count
          this.stayStorageCount = data.stay_storage_count
          this.storageCount = data.storage_count
          this.pendingCount = data.pending_count
          this.sellGoodsCount = data.sell_goods_count
          this.stockOutGoodsCount = data.stockout_goods_count
        })
      },

      setOrderList(data) {
        if (data.total && data.order_list && data.order_list.length > 0) {
          this.orderListTotal = data.total
          this.orderList = data.order_list
        }
      },

      // 查看明细
      checkDetail(row) {
        const purchaseNo = row.purchase_no

        this.$router.push(`/funds/${purchaseNo}`)
      },

      handleCurrentChange(page) {
        this.loading = true

        this.$router.push({
          path: '/',
          query: {
            page,
          },
        })

        index(page).then(res => {
          this.loading = false
          this.setOrderList(res.data)
          this.setUnPayMoney(res.data)
        })

        window.scroll(0, 0)
      },

      setUnPayMoney(data) {
        const orderCount = data.order_count
        const count = orderCount && orderCount.length > 0
          ? orderCount.filter(item => item.trade_status === 5)
          : []

        this.unPayMoney = count.length > 0 ? Number(count[0].money_account) / 100 : 0
      },
    },

    computed: {
      ...mapState({
        userInfo: state => state.user.userInfo,
      }),

      ...mapGetters(['accountType', 'funds']),
    },
  }
</script>

<style lang="scss" scoped>
  @import "./home.scss";
</style>

<style lang="scss">
  $theme-color: #cdae63;

  .pay-list-body {
    /* 表格 */
    .table {
      thead {
        color: #666;
        font-weight: normal;

        th {
          height: 40px !important;
          padding: 0;
          line-height: 40px;
          border-bottom: 1px solid #d1d1d1 !important;
        }
      }

      tbody {
        td {
          height: 50px !important;
          line-height: 50px;
          border-bottom: 1px dashed #e9e9e9 !important;

          .cell {
            font-size: 12px;
          }
        }

        /* 表格里的链接*/
        .operate-link {
          margin-left: 10px;
          color: $theme-color;
          text-decoration: none;
        }
      }
    }
  }
</style>