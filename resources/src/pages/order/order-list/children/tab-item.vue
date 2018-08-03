<template>
  <div class="tab-item">
    <table border="0" cellspacing="0" cellpadding="0">
      <thead class="thead">
      <tr>
        <td style="width: 270px;">货品</td>
        <td>单价(HK$)</td>
        <td>数量</td>
        <td style="width: 135px;">金额(HK$)</td>
        <td style="width: 145px;">状态</td>
        <td style="width: 135px;">操作</td>
      </tr>
      </thead>
      <tr class="export">
        <td colspan='6'>
          <el-dropdown size="small" split-button type="primary" v-if="accountType || order.length &&
          order[0]['operator_types'].includes(2)">
            导出采购单
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item :disabled='check_list.length == 0' @click.native="exportSelect">导出已选采购单</el-dropdown-item>
              <el-dropdown-item @click.native="exportAll">导出全部采购单</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </td>
      </tr>
      <tbody class="tbody" v-for="tableItem in data">
      <tr class="order-info">
        <td colspan='6'>
          <div class="buy-order-size">
            <el-checkbox v-model="tableItem.is_check" @change="checkOrder" :id="tableItem.purchase_no">
              采购单编号： <span>{{tableItem.purchase_no}}</span>
            </el-checkbox>
          </div>
          <div class="order-time">
            下单时间：<span>{{ tableItem.create_time }}</span>
          </div>
        </td>
      </tr>
      <tr class="product-item" v-for="(goodsItem, index) in tableItem.goods_info_list" :key="index">
        <td>
          <div class="goods_info">
            <div class="td-left" :style="'background-image:url('+goodsItem.cover_image.new_path+')'">
              <!--<img :src="goodsItem.cover_image.new_path" alt="">-->
            </div>
            <div class="td-right">
              <!--<span>{{goodsItem.goods_name}}</span>-->
              <el-tooltip class="item" effect="dark" placement="top-start">
                <div slot="content"><pre>{{goodsItem.goods_name}}</pre></div>
                <span><pre class="table-goods-name">{{goodsItem.goods_name}}</pre></span>
              </el-tooltip>
              <span>颜色: {{ goodsItem.sku_color }}</span>
              <span>规格: {{ goodsItem.sku_spec }}</span>
            </div>
          </div>
        </td>
        <td>{{ goodsItem.new_sku_price }}</td>
        <td>{{ goodsItem.goods_num }}</td>
        <td v-if="tableItem.goods_info_list.length == 1">
          <b>HK$ {{ tableItem.new_trade_price }}</b>
          （共{{ tableItem.goods_num }}件)
        </td>
        <td v-else-if="index === 0" :rowspan="tableItem.goods_info_list.length">
          <b>HK$ {{ tableItem.new_trade_price }}</b>
          （共{{ tableItem.goods_num }}件)
        </td>
        <td v-if="tableItem.goods_info_list.length === 1">
            <b>{{ tableItem.trade_status_text }}</b>
            <a href="javascript:void(0);" class="link-active" @click="toOrderDetail(tableItem.purchase_no)">
              采购单详情
            </a>
        </td>
        <td v-else v-else-if="index === 0" :rowspan="tableItem.goods_info_list.length">
          <b>{{ tableItem.trade_status_text }}</b>
          <a href="javascript:void(0);" class="link-active" @click="toOrderDetail(tableItem.purchase_no)">
            采购单详情
          </a>
        </td>
        <td class="view-detail" v-if="tableItem.goods_info_list.length === 1">
          <span class="confirm-order" v-if="(tableItem.trade_status === 1) || (tableItem.trade_status === 2) || (tableItem.trade_status === 3) ||
          (tableItem.trade_status === 4)" @click="toOrderDetail(tableItem.purchase_no)">
            编辑采购单
          </span>
          <span class="confirm-order" v-if="(accountType || order.length && order[0]['operator_types'].includes(2) ||
          order[0]['operator_types'].includes(3)) && (tableItem.trade_status === 1 || tableItem.trade_status === 4)"
                @click="_cancelOrder(tableItem.trade_status, tableItem.purchase_no)">
            撤销采购单
          </span>
          <span class="confirm-order" v-if="(accountType || order.length && order[0]['operator_types'].includes(2) ||
          order[0]['operator_types'].includes(3)) && tableItem.trade_status === 5" @click="toStorageOrder(tableItem.purchase_no)">
            查看入库单
          </span>
        </td>
        <td class="view-detail" v-else-if="index === 0" :rowspan="tableItem.goods_info_list.length">
          <span class="confirm-order" v-if="(tableItem.trade_status === 1) || (tableItem.trade_status === 2) ||
          (tableItem.trade_status === 3) || (tableItem.trade_status === 4)" @click="toOrderDetail(tableItem.purchase_no)">
            编辑采购单
          </span>
          <span class="confirm-order" v-if="(accountType || order.length && order[0]['operator_types'].includes(2) ||
          order[0]['operator_types'].includes(3)) && (tableItem.trade_status === 1 || tableItem.trade_status === 4)"
                @click="_cancelOrder(tableItem.trade_status, tableItem.purchase_no)">
            撤销采购单
          </span>
          <span class="confirm-order" v-if="(accountType || order.length && order[0]['operator_types'].includes(2) ||
          order[0]['operator_types'].includes(3)) && tableItem.trade_status === 5" @click="toStorageOrder(tableItem.purchase_no)">
            查看入库单
          </span>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
  import { mapState, mapGetters } from 'vuex'
  import { cancelOrder } from '@/api/order'
  import { MessageBox } from 'element-ui'
  import { exportExcel } from '@/utils/excel'
  import { param } from '@/utils/param'
  import { BASE_API } from '@/config/env'

  export default {
    name: 'tab-item',
    props: [
      'data',
      'purchase_no',
      'trade_status',
      'start_time',
      'end_time',
      'logistic_method',
      'code',
      'goods_name',
      'product_model',
      'check_list',
  ],
    data() {
      return {}
    },
    computed: {
      ...mapState({
        userInfo: state => state.user.userInfo,
      }),
      ...mapGetters(['accountType', 'order']),
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

      checkOrder(val, e) {
        const tradeNo = e.target.labels[0].id

        if (val) {
          this.check_list.push(tradeNo)
        } else {
          this.check_list.splice(this.check_list.indexOf(tradeNo), 1)
        }
      },

      exportSelect() {
        const checkList = this.check_list
        const exportParams = {
          check_list: checkList,
        }
        const originUrl = `${BASE_API}/api/order/orderList/exportExcel`
        const exportUrl = `${originUrl}${originUrl.indexOf('?') === -1 ? '?' : '&'}${param(exportParams)}`

        exportExcel(exportUrl)
      },

      exportAll() {
        let startTime = ''
        let endTime = ''
        let startTimeStamp, endTimeStamp
        const purchaseNo = this.purchase_no ? this.purchase_no : ''
        const purchaseStatus = this.trade_status ? this.trade_status : ''
        const goodsName = this.goods_name ? this.goods_name : ''
        const productModel = this.product_model ? this.product_model : ''
        const code = this.code ? this.code : ''

        if (this.start_time) {
          startTimeStamp = new Date(this.start_time)
          startTime = this.formatDate(startTimeStamp)
        }
        if (this.end_time) {
          endTimeStamp = new Date(this.end_time)
          endTime = this.formatDate(endTimeStamp)
        }

        if (endTimeStamp - startTimeStamp < 0) {
          this.$message.error('结束时间需大于起始时间')
          this.isLoading = false

          return false
        }
        const exportParams = {
          purchase_no: purchaseNo,
          purchase_status: purchaseStatus,
          start_time: startTime,
          end_time: endTime,
          goods_name: goodsName,
          code,
          product_model: productModel,
          logistic_method: this.logistic_method,
        }
        const originUrl = `${BASE_API}/api/order/orderList/exportExcel`
        const exportUrl = `${originUrl}${originUrl.indexOf('?') === -1 ? '?' : '&'}${param(exportParams)}`

        exportExcel(exportUrl)
      },

      toStorageOrder(purchaseNo) {
        this.$router.push({
          path: `/order/purchase-order-detail?purchase_no=${purchaseNo}`,
        })
      },

      toOrderDetail(purchaseNo) {
        this.$router.push({
          path: `/order/order-detail?purchase_no=${purchaseNo}`,
        })
      },

      _cancelOrder(trade_status, purchase_no) {
        const params = {
          trade_status,
          purchase_no,
          operator_id: this.userInfo.user_no,
        }

        MessageBox.confirm('该操作将撤销该订单, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
        }).then(() => {
          cancelOrder(params)
            .then(res => {
              this.$message.success('撤销成功')
              this.$emit('reciveSuccess')
            })
            .catch(error => {
              this.$message.error('撤销失败')
            })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消撤销',
          })
        })
      },
    },
  }
</script>
<style lang="scss" scoped>
  @import "./tab-item.scss";
</style>
