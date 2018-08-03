<template>
  <div class="page">
    <div class="location">
      <span>订单管理</span>
      <span>/</span>
      <span>分销订单</span>
      <span>/</span>
      <span>分销订单详情</span>
    </div>

    <div class="page-content" v-loading="isLoading">
      <div class="page-content-first">
        <!-- 订单状态 -->
        <div class="distribution-status">
          <p :class="detailData.trade_status >= 3 && detailData.trade_status != 6 && detailData.trade_status != 7
          && detailData.trade_status != 8 && detailData.trade_status != 9 ? 'status-text on' : 'status-text'">已付款</p>
          <p class="status-line done" v-if="detailData.trade_status >= 3 && detailData.trade_status != 6
          && detailData.trade_status != 7 && detailData.trade_status != 8 && detailData.trade_status != 9"></p>
          <p class="status-line doing" v-if="detailData.trade_status == 2"></p>
          <p class="status-line pending" v-if="detailData.trade_status < 2 || detailData.trade_status == 6
          || detailData.trade_status == 7 || detailData.trade_status == 8 || detailData.trade_status == 9"></p>

          <p :class="detailData.trade_status >= 4 && detailData.trade_status != 6 && detailData.trade_status != 7
          && detailData.trade_status != 8 && detailData.trade_status != 9 ? 'status-text on' : 'status-text'">发货出库</p>
          <p class="status-line done" v-if="detailData.trade_status > 4 && detailData.trade_status != 6
          && detailData.trade_status != 7 && detailData.trade_status != 8 && detailData.trade_status != 9"></p>
          <p class="status-line doing" v-if="detailData.trade_status == 4"></p>
          <p class="status-line pending" v-if="detailData.trade_status < 4 || detailData.trade_status == 6
          || detailData.trade_status == 7 || detailData.trade_status == 8 || detailData.trade_status == 9"></p>

          <p :class="detailData.trade_status == 5 ? 'status-text on' : 'status-text'">确认收货</p>
          <p class="status-line done" v-if="detailData.trade_status >= 5 && detailData.trade_status != 6
          && detailData.trade_status != 7 && detailData.trade_status != 8 && detailData.trade_status != 9"></p>
          <!--<p class="status-line doing" v-if="detailData.trade_status == 5"></p>-->
          <p class="status-line pending" v-if="detailData.trade_status < 5 || detailData.trade_status == 6
          || detailData.trade_status == 7 || detailData.trade_status == 8 || detailData.trade_status == 9"></p>
          <p :class="detailData.trade_status == 5 ? 'status-text on' : 'status-text'">完成</p>
        </div>

        <!-- 交易信息 -->
        <div class="distribution-trade">
          <div class="trade-info">
            <p class="trade-status">当前订单状态：
              <span class="trade-status-on" v-if="detailData.trade_status == 2">待付款</span>
              <span class="trade-status-on" v-if="detailData.trade_status == 3">待发货</span>
              <span class="trade-status-on" v-if="detailData.trade_status == 4">待签收</span>
              <span class="trade-status-on" v-if="detailData.trade_status == 5">交易完成</span>
              <span class="trade-status-on" v-if="detailData.trade_status == 7">商家发货失败</span>
              <span class="trade-status-on" v-if="detailData.trade_status == 8">退款成功</span>
              <span class="trade-status-on" v-if="detailData.trade_status == 9">买家已取消订单</span>
            </p>
            <p class="trade-order">货单编号：{{ detailData.trade_no }}</p>
            <p class="trade-order">交易方式：<span class="trade-mode-img"></span>{{ detailData.pay_channel_cn }}</p>
            <p class="trade-mode">交易号：{{ detailData.out_pay_no }}</p>
          </div>
          <div class="trade-launch">
            <p class="trade-launch-btn" data-id="2" @click="tabChange"
               v-if="(accountType || (settingOrder.length && (settingOrder[0]['operator_types'].includes(2)
               || settingOrder[0]['operator_types'].includes(3)))) && detailData.trade_status == 3">立即发货</p>
          </div>
        </div>
      </div>
      <div class="page-content-second">
        <!-- tab切换 -->
        <div class="distribution-tab">
          <p :class="tabStatus == 1 ? 'tab-item on' : 'tab-item'" data-id="1" @click="tabChange">订单信息</p>
          <p :class="tabStatus == 2 ? 'tab-item on' : 'tab-item'" data-id="2" @click="tabChange">配送物流</p>
        </div>
        <!-- 订单信息 -->
        <div class="distribution-order" v-if="tabStatus == 1 && detailData.recipients_info && detailData.buyer_info">
          <div class="order-info">
            <div class="order-receiver">
              <p class="order-receiver-title">收件人信息</p>
              <p class="order-receiver-name">收件人姓名：{{ detailData.recipients_info.recipient_name }}</p>
              <p class="order-receiver-address">收货地址：{{ detailData.recipients_info.recipient_address }}</p>
              <p class="order-receiver-phone">手机：{{ detailData.recipients_info.recipient_mobilephone }}</p>
            </div>
            <div class="order-buyer">
              <p class="order-buyer-title">买家信息</p>
              <p class="order-buyer-seller">分销商：{{ detailData.buyer_info.retailer_no }}</p>
              <p class="order-buyer-name">登录名：{{ detailData.buyer_info.retailer_name }}</p>
              <p class="order-buyer-phone">手机：{{ detailData.buyer_info.mobile_account }}</p>
            </div>
          </div>

          <table class="detail-list">
            <tr class="detail-list-title">
              <th>货品规格</th>
              <th>销售价（HK$）</th>
              <th>数量</th>
              <th>税</th>
              <th>运费（元）</th>
              <th>优惠（HK$）</th>
              <th>货品状态</th>
              <th>小计</th>
            </tr>
            <tr class="detail-item" v-for="goodsItem in detailData.goods_list" v-if="goodsItem.goods_num > 0">
              <td class="detail-item-first">
                <img class="detail-item-img" :src="goodsItem.cover_image.new_path" alt="商品图片">
                <div class="detail-item-goods-info">
                  <el-tooltip class="item" effect="dark" placement="top-start">
                    <div slot="content"><pre>{{goodsItem.goods_name}}</pre></div>
                    <p><pre class="detail-item-goods-name">{{ goodsItem.goods_name }}</pre></p>
                  </el-tooltip>
                  <p class="detail-item-goods-color">颜色：{{ goodsItem.sku_color }}</p>
                  <p class="detail-item-goods-color">规格：{{ goodsItem.sku_spec }}</p>
                </div>
              </td>
              <td>{{ goodsItem.new_sku_price }}</td>
              <td>{{ goodsItem.goods_num }}</td>
              <td>{{ goodsItem.new_customs_duties }}</td>
              <td>{{ goodsItem.new_shipping_rate }}</td>
              <td>{{ goodsItem.new_discount_price }}</td>
              <td>{{ goodsItem.goods_status }}</td>
              <td>{{ goodsItem.new_pay_price }}</td>
            </tr>
          </table>
          <div class="distribution-remark">
            <div class="distribution-remark-title">订单备注</div>
            <div class="distribution-remark-content">
              <el-input class="distribution-remark-input"
                        v-model="comment"
                        :placeholder="detailData.trade_comment ? detailData.trade_comment : '填写备注信息'"
                        @blur="editComment"></el-input>
            </div>
          </div>
          <div class="detail-price">
            <div class="detail-price-content">
              <p class="detail-price-subtotal"><span>税前小计</span><span>{{ detailData.new_goods_price_total }}</span></p>
              <p class="detail-price-subtotal"><span>税</span><span>{{ detailData.new_customs_duties_total }}</span></p>
              <p class="detail-price-subtotal"><span>优惠</span><span>{{ detailData.new_discount_price_total }}</span></p>
              <p class="detail-price-subtotal"><span>运费</span><span>{{ detailData.new_shipping_rate_total }}</span></p>
              <p class="detail-price-total"><span>应收款</span><span>{{ detailData.new_trade_price }}</span></p>
              <p class="detail-price-capital"><span>实付款</span><span>{{ detailData.new_pay_price }}</span></p>
            </div>
          </div>
          <div class="distribution-time">
            <p class="distribution-time-order">下单时间：{{ detailData.create_time }}</p>
            <p class="distribution-time-pay">付款时间：{{ detailData.pay_time }}</p>
          </div>
        </div>
        <!-- 配送物流 -->
        <div class="distribution-logistics"
             v-if="detailData.logistic_recipients_list && detailData.logistic_recipients_list.length && tabStatus == 2">
          <div class="logistics-item" v-for="(logisticItem, logisticIndex) in detailData.logistic_recipients_list">
            <div class="logistics-head">
              <p class="logistics-head-title">代发地址{{ logisticIndex+1 }}</p>
              <p class="logistics-head-desc">{{ detailData.trade_status_cn }}</p>
            </div>
            <div class="logistics-body">
              <div class="logistics-receiver">
                <p class="logistics-receiver-title">收件人信息</p>
                <p class="logistics-receiver-name">收件人姓名：{{ logisticItem.recipient_name }}</p>
                <p class="logistics-receiver-address">收货地址： {{ logisticItem.recipient_address }}</p>
                <p class="logistics-receiver-ceil">手机：{{ logisticItem.recipient_mobilephone }}</p>
              </div>
              <div class="logistics-company" v-if="detailData.trade_status == 3">
                <div class="logistics-select" v-if="detailData.logistic_method != 1">
                  <p class="logistics-select-title">发货物流：</p>
                  <el-select v-model="companyCode[logisticIndex]" placeholder="请选择物流公司"
                             @change="logisCompany(logisticIndex, logisticItem.logistic_address_id)">
                    <el-option
                      v-for="companyItem in detailData.transport_company"
                      :key="companyItem.company_code"
                      :label="companyItem.name_c"
                      :value="companyItem.company_code">
                    </el-option>
                  </el-select>
                </div>
                <div class="logistics-num" v-if="detailData.logistic_method != 1">
                  <p class="logistics-num-title">运单号码：</p>
                  <el-input v-model="packageNo[logisticIndex]" size="mini" class="logistics-num-input"
                            @blur="logisPackage(logisticIndex)"></el-input>
                </div>
                <div class="logistics-confirm">
                  <el-button class="logistics-confirm-btn" type="primary"
                             v-if="(accountType || (settingOrder.length && (settingOrder[0]['operator_types'].includes(2)
                             || settingOrder[0]['operator_types'].includes(3))))
                             && (logisticIndex == detailData.logistic_recipients_list.length-1)"
                             @click="launch">确认发货</el-button>
                </div>
              </div>
              <div class="logistics-company" v-else-if="logisticItem.transport_company">
                <p class="logistics-company-name">物流公司：{{ logisticItem.transport_company }}</p>
                <p class="logistics-company-num">运单号码：{{ logisticItem.package_no }}</p>
                <p class="logistics-company-time">发货时间：{{ detailData.delivery_time }}</p>
                <div class="logistics-company-status">
                  <p class="logistics-company-status-title">物流跟踪：</p>
                  <div class="logistics-company-status-detail" v-if="logisticItem.logistic_detail && logisticItem.logistic_detail.length">
                    <p class="logistics-company-status-item" v-for="logisDetail in logisticItem.logistic_detail">
                      {{ logisDetail.time}} {{ logisDetail.context }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="logistics-goods" v-if="logisticItem.goods_list && logisticItem.goods_list.length">
              <p class="logistics-goods-title">包含如下货品</p>
              <div class="logistics-goods-wrap">
                <div class="logistics-goods-item" v-for="logisGoods in logisticItem.goods_list">
                  <div class="logistics-goods-brief">
                    <div class="logistics-goods-img" :style="{backgroundImage: 'url(' + logisGoods.cover_image.new_path + ')'}"></div>
                    <div class="logistics-goods-info">
                      <p class="logistics-goods-name">{{ logisGoods.goods_name }}</p>
                      <p class="logistics-goods-color">颜色：{{ logisGoods.sku_color }}</p>
                      <p class="logistics-goods-spec">规格：{{ logisGoods.sku_spec }}</p>
                    </div>
                  </div>
                  <div class="logistics-goods-amount">数量 ：{{ logisGoods.goods_num }} 件</div>
                  <div class="logistics-goods-rf" v-if="logisGoods.rfid_list && logisGoods.rfid_list.length">
                    <p class="logistics-goods-rf-title">RFID:</p>
                    <p class="logistics-goods-rf-item" v-for="goodsRf in logisGoods.rfid_list">{{ goodsRf }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- 立即发货 -->
        <div class="distribution-launch"
             v-if="detailData.logistic_recipients_list && detailData.logistic_recipients_list.length && tabStatus == 3">
          <div class="logistics-item" v-for="logisticItem in detailData.logistic_recipients_list">
            <div class="logistics-head">
              <p class="logistics-head-title">代发地址1</p>
              <p class="logistics-head-desc"></p>
            </div>
            <div class="logistics-body">
              <div class="logistics-receiver">
                <p class="logistics-receiver-title">收件人信息</p>
                <p class="logistics-receiver-name">收件人姓名：{{ logisticItem.recipient_name }}</p>
                <p class="logistics-receiver-address">收货地址：{{ logisticItem.recipient_address }}</p>
                <p class="logistics-receiver-ceil">手机：{{ logisticItem.recipient_mobilephone }}</p>
              </div>
            </div>
            <div class="logistics-goods" v-if="logisticItem.goods_list && logisticItem.goods_list.length">
              <p class="logistics-goods-title">包含如下货品</p>
              <div class="logistics-goods-wrap">
                <div class="logistics-goods-item" v-for="logisGoods in logisticItem.goods_list">
                  <div class="logistics-goods-brief">
                    <div class="logistics-goods-img" :style="{backgroundImage: 'url(' + logisGoods.cover_image.new_path + ')'}"></div>
                    <div class="logistics-goods-info">
                      <p class="logistics-goods-name">{{ logisGoods.goods_name }}</p>
                      <p class="logistics-goods-color">颜色：{{ logisGoods.sku_color }}</p>
                      <p class="logistics-goods-spec">规格：{{ logisGoods.sku_spec }}</p>
                    </div>
                  </div>
                  <div class="logistics-goods-amount">数量 ：{{ logisGoods.goods_num }} 件</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { getDistributionDetail, editComment, launch } from '@/api/distribution'

  export default {
    name: 'distributionDetail',

    data() {
      return {
        isLoading: true,
        detailData: {},
        comment: '',
        companyCode: [],
        packageNo: [],
        deliveryLogistic: [],
        tabStatus: 1,
      }
    },

    created() {
      let tradeNo = this.$route.query.trade_no

      this.getDetailData(tradeNo)
    },
    computed: {
      ...mapState({
        userInfo: state => state.user.userInfo,
      }),

      ...mapGetters(['accountType', 'settingOrder']),
    },
    methods: {
      getDetailData(tradeNo) {
        getDistributionDetail(tradeNo).then(res => {
          this.detailData = res.data.retailTradeInfo
          this.isLoading = false
        })
      },

      tabChange(e) {
        this.tabStatus = e.currentTarget.getAttribute('data-id')
      },

      editComment() {
        if (this.comment === '') {
          this.$message.error('备注内容不能为空')

          return
        }

        let params = {
          trade_no: this.detailData.trade_no,
          seller_comment: this.comment,
        }

        editComment(params).then(res => {
          this.$message.success('操作成功')
        })
          .catch(error => {
            this.$message.error('操作失败')
          })
      },

      logisCompany(index, addressId) {
        let delivery = {}

        delivery.company_code = this.companyCode[index]
        delivery.logistic_address_id = addressId

        for(let i in this.detailData.transport_company) {
          if (this.detailData.transport_company[i].company_code == this.companyCode[index]) {
            delivery.transport_company = this.detailData.transport_company[i].name_c
          }
        }
//        delivery.goods_voucher = this.detailData.logistic_recipients_list[index].goods_voucher
        this.deliveryLogistic[index] = delivery
      },

      logisPackage(index) {
        let delivery = {}

        delivery.package_no = this.packageNo[index]
        this.deliveryLogistic[index] = Object.assign(this.deliveryLogistic[index], delivery)
      },

      launch() {
        let tradeNo = this.detailData.trade_no
        let deliveryInfo = this.deliveryLogistic.length ? this.deliveryLogistic : null
        let params = {
          trade_no: tradeNo,
          delivery_logistic_info: deliveryInfo,
        }

        launch(params).then(res => {
          this.$message.success('操作成功')
          this.getDetailData(tradeNo)
        })
          .catch(error => {
            this.$message.error('操作失败')
          })
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import 'distribution-detail';
</style>
