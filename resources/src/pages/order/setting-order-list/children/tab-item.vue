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
          <el-dropdown size="small" split-button type="primary" v-if="accountType || settingOrder.length &&
          settingOrder[0]['operator_types'].includes(2)">
            导出订单
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item :disabled="checked_list.length == 0" @click.native="exportSelect">导出已选订单</el-dropdown-item>
              <el-dropdown-item @click.native="exportAll">导出全部订单</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </td>
      </tr>
      <tbody class="tbody" v-for="(tableItem, tradeIndex) in data" :key="tradeIndex">
      <tr class="order-info">
        <td colspan='6'>
          <div class="buy-order-size">
            <el-checkbox v-model="tableItem.is_check" @change="checkOrder" :id="tableItem.trade_no">
            订单编号： <span>{{tableItem.trade_no}}</span></el-checkbox>
          </div>
          <div class="order-time">
            下单时间：<span>{{ tableItem.create_time }}</span>
          </div>
        </td>
      </tr>
      <tr class="product-item" v-for="(goodsItem, index) in tableItem.trade_detail" :key="index">
        <td>
          <div class="goods_info">
            <div class="td-left" :style="'background-image:url('+goodsItem.cover_image.new_path+')'">
              <!--<img :src="goodsItem.cover_image.new_path" alt="">-->
            </div>
            <div class="td-right">
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
        <td>{{ goodsItem.goods_sku_num }}</td>
        <td v-if="tableItem.trade_detail.length == 1 && !tableItem.changeFlag">
          <b>HK$ {{ tableItem.new_pay_price }}</b>
          （共{{ tableItem.goods_num }}件)
          <p>含运费 {{ tableItem.new_shipping_rate_total }}</p>
          <p class="changePrice" v-if="(accountType || settingOrder.length && settingOrder[0]['operator_types'].includes(2)
          || settingOrder[0]['operator_types'].includes(3)) && tableItem.trade_status === 2" @click="_changePrice(tableItem.trade_no,
          tableItem.new_pay_price, tableItem.new_shipping_rate_total)">
            修改价格
          </p>
        </td>
        <td v-else-if="tableItem.trade_detail.length == 1 && tableItem.changeFlag" class="changeInput">
          <label>总金额</label>
          <el-input v-model.number="tableItem.new_pay_price" placeholder="" size="mini" :clearable=true></el-input>
          <label>运费</label>
          <el-input v-model.number="tableItem.new_shipping_rate_total" placeholder="" size="mini" :clearable=true></el-input>
          <el-button style="margin-top: 5px;" type="primary" size="mini" @click="sureChange(tableItem.trade_no, tableItem.new_pay_price,
          tableItem.new_shipping_rate_total)">
            确认修改
          </el-button>
        </td>
        <td v-else-if="index == 0 && !tableItem.changeFlag" :rowspan="tableItem.trade_detail.length">
          <b>HK$ {{ tableItem.new_pay_price }}</b>
          （共{{ tableItem.goods_num }}件)
          <p>含运费 {{ tableItem.new_shipping_rate_total }}</p>
          <p class="changePrice" v-if="(accountType || settingOrder.length && settingOrder[0]['operator_types'].includes(2)
          || settingOrder[0]['operator_types'].includes(3)) && tableItem.trade_status === 2" @click="_changePrice(tableItem.trade_no,
          tableItem.new_pay_price, tableItem.new_shipping_rate_total)">修改价格</p>
        </td>
        <td v-else-if="index == 0 && tableItem.changeFlag" :rowspan="tableItem.trade_detail.length">
          <label>总金额</label>
          <el-input v-model.number="tableItem.new_pay_price" placeholder="" :clearable=true></el-input>
          <label>运费</label>
          <el-input v-model.number="tableItem.new_shipping_rate_total" placeholder="" :clearable=true></el-input>
          <el-button type="primary" size="mini" @click="sureChange(tableItem.trade_no, tableItem.new_pay_price,
          tableItem.new_shipping_rate_total)">
            确认修改
          </el-button>
        </td>
        <td v-if="tableItem.trade_detail.length == 1">
          <b>{{ tableItem.trade_status_text }}</b>
          <a href="javascript:void(0);" v-if="tableItem.trade_status === 4" class="link-active" @click="toOrderDetail(tableItem.trade_no)">
            查看物流
          </a>
          <a href="javascript:void(0);" v-if="tableItem.trade_status === 3" class="link-active">
            采购入库中
          </a>
        </td>
        <td v-else v-else-if="index == 0" :rowspan="tableItem.trade_detail.length">
          <b>{{ tableItem.trade_status_text }}</b>
          <!--</router-link>-->
          <a href="javascript:void(0);" v-if="tableItem.trade_status === 4" class="link-active" @click="toOrderDetail(tableItem.trade_no)">
            查看物流
          </a>
          <a href="javascript:void(0);" v-if="tableItem.trade_status === 3" class="link-active">
            采购入库中
          </a>
        </td>
        <td class="view-detail" v-if="tableItem.trade_detail.length == 1">
          <span class="send-order" v-if="(accountType || settingOrder.length && settingOrder[0]['operator_types'].includes(2)
          || settingOrder[0]['operator_types'].includes(3)) && tableItem.trade_status === 3" @click="toOrderDetail(tableItem.trade_no)">
            待发货
          </span>
          <span class="view-order" @click="toOrderDetail(tableItem.trade_no)">
            查看详情
          </span>
          <span class="end-order" v-if="(accountType || settingOrder.length && settingOrder[0]['operator_types'].includes(2)
          || settingOrder[0]['operator_types'].includes(3)) && tableItem.trade_status === 3" @click="_outOrder(tableItem.trade_no, tableItem.pay_price)">
            缺货退款
          </span>
        </td>
        <td class="view-detail" v-else-if="index == 0" :rowspan="tableItem.trade_detail.length">
          <span class="send-order" v-if="(accountType || settingOrder.length && settingOrder[0]['operator_types'].includes(2)
          || settingOrder[0]['operator_types'].includes(3)) && tableItem.trade_status === 3" @click="toOrderDetail(tableItem.trade_no)">
            待发货
          </span>
          <span class="view-order" @click="toOrderDetail(tableItem.trade_no)">
            查看详情
          </span>
          <span class="end-order" v-if="(accountType || settingOrder.length && settingOrder[0]['operator_types'].includes(2)
          || settingOrder[0]['operator_types'].includes(3)) && tableItem.trade_status === 3" @click="_outOrder(tableItem.trade_no, tableItem.pay_price)">
            缺货退款
          </span>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex'
  import { cancelOrder, outOrder, changePrice } from '@/api/order'
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
      'over_time',
      'logistic_method',
      'code',
      'goods_name',
      'brand_no',
      'checked_list',
    ],
    computed: {
      ...mapGetters(['accountType', 'settingOrder']),
    },
    data() {
      return {
        payPriceOld: 0,
        payPriceNew: 0,
        oldShippingRate: 0,
        newShippingRate: 0,
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

      checkOrder(val, e) {
        const tradeNo = e.target.labels[0].id

        if (val) {
          this.checked_list.push(tradeNo)
        } else {
          this.checked_list.splice(this.checked_list.indexOf(tradeNo), 1)
        }
      },

      exportSelect() {
        const checkList = this.checked_list
        const exportParams = {
          check_list: checkList,
        }
        const originUrl = `${BASE_API}/api/order/settingOrderList/exportExcel`
        const exportUrl = `${originUrl}${originUrl.indexOf('?') === -1 ? '?' : '&'}${param(exportParams)}`

        exportExcel(exportUrl)
      },

      exportAll() {
        let startTime = ''
        let overTime = ''
        let exportParams = {}
        let startTimeStamp, overTimeStamp
        const purchaseNo = this.purchase_no ? this.purchase_no : ''
        const purchaseStatus = this.trade_status ? this.trade_status : ''
        const commodityName = this.goods_name ? this.goods_name : ''
        const brandNo = this.brand_no ? this.brand_no : ''
        const code = this.code ? this.code : ''
        const logisticMethod = this.logistic_method ? this.logistic_method : ''

        if (this.start_time) {
          startTimeStamp = new Date(this.start_time)
          startTime = this.formatDate(startTimeStamp)
        }
        if (this.over_time) {
          overTimeStamp = new Date(this.over_time)
          overTime = this.formatDate(overTimeStamp)
        }

        if (overTimeStamp - startTimeStamp < 0) {
          this.$message.error('结束时间需大于起始时间')
          this.isLoading = false

          return false
        }

        if (purchaseStatus === '' && commodityName === '' && brandNo === '' && code === '' && logisticMethod === '' &&
          startTime === '' && overTime === '') {
          exportParams = {
            trade_no: purchaseNo,
            is_search: 0,
          }
        } else {
          exportParams = {
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
        const originUrl = `${BASE_API}/api/order/settingOrderList/exportExcel`
        const exportUrl = `${originUrl}${originUrl.indexOf('?') === -1 ? '?' : '&'}${param(exportParams)}`

        exportExcel(exportUrl)
      },

      _outOrder(tradeNo, payPrice) {
        const params = {
          trade_no: tradeNo,
          refund_fee: payPrice,
        }

        MessageBox.confirm('该操作将执行缺货退款操作, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
        }).then(() => {
          outOrder(params)
            .then(res => {
              this.$message.success('操作成功')
              this.$emit('outOrder')
            })
            .catch(error => {
              this.$message.error('操作失败')
            })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消操作',
          })
        })
      },
      toOrderDetail(purchaseNo) {
        this.$router.push({
          path: `/distribution/distribution-detail?trade_no=${purchaseNo}`,
        })
      },

      _changePrice(tradeNo, payPriceOld, oldShippingRate) {
        this.payPriceOld = payPriceOld
        this.oldShippingRate = oldShippingRate
        this.data.forEach(item => {
          if (item.trade_no === tradeNo) {
            item.changeFlag = true
          }
        })
      },

      sureChange(tradeNo, newTradePrice, newShippingRate) {
        if (newTradePrice === '') {
          this.$message.error('总金额不能为空')

          return false
        }
        if (newShippingRate === '') {
          this.$message.error('运费不能为空')

          return false
        }

        const params = {
          trade_no: tradeNo,
          pay_price_old: this.payPriceOld * 100,
          pay_price_new: newTradePrice * 100,
          old_shipping_rate: this.oldShippingRate * 100,
          new_shipping_rate: newShippingRate * 100,
        }

        changePrice(params)
          .then(res => {
            if (res.data.resultEditPrice.data.msg === 'order pay price changed') {
              this.$message.success('价格修改成功')
              this.$emit('successChange')
            } else {
              this.$message.error('价格修改失败')
              this.data.forEach(item => {
                if (item.trade_no === tradeNo) {
                  item.new_pay_price = this.payPriceOld
                  item.new_shipping_rate_total = this.oldShippingRate
                }
              })
            }

            this.data.forEach(item => {
              if (item.trade_no === tradeNo) {
                item.changeFlag = false
              }
            })
          })
          .catch(error => {
            this.$message.error(error)
          })
      },
    },
  }
</script>
<style lang="scss" scoped>
  @import "./tab-item.scss";
</style>
