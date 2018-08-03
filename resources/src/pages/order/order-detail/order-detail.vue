<template>
  <div class="page">
    <!--面包屑导航-->
    <of-breadcrumb :items="breadcrumb_items"></of-breadcrumb>

    <div style="width: 100%; height: 5px"></div>

    <div class="page-content" v-loading="isLoading">
      <!-- 订单状态 -->
      <div class="order-status">
        <p :class="detailData.purchase_status >= 2 && detailData.purchase_status != 6 && detailData.purchase_status
        != 7 && detailData.purchase_status != 8 && detailData.purchase_status != 9 ? 'status-text on' : 'status-text'">采购入库单确认</p>
        <p class="status-line pending" v-if="detailData.purchase_status < 2 || detailData.purchase_status == 6
        || detailData.purchase_status == 7 || detailData.purchase_status == 8 || detailData.purchase_status == 9"></p>
        <p class="status-line doing" v-if="detailData.purchase_status == 2"></p>
        <p class="status-line done" v-if="detailData.purchase_status > 2 && detailData.purchase_status != 6
        && detailData.purchase_status != 7 && detailData.purchase_status != 8 && detailData.purchase_status != 9"></p>

        <p :class="detailData.purchase_status >= 4 && detailData.purchase_status != 6 && detailData.purchase_status
        != 7 && detailData.purchase_status != 8 && detailData.purchase_status != 9 ? 'status-text on' : 'status-text'">出库</p>
        <p class="status-line pending" v-if="detailData.purchase_status < 4 || detailData.purchase_status == 6
        || detailData.purchase_status == 7 || detailData.purchase_status == 8 || detailData.purchase_status == 9"></p>
        <p class="status-line doing" v-if="detailData.purchase_status == 4"></p>
        <p class="status-line done" v-if="detailData.purchase_status > 4 && detailData.purchase_status != 6
        && detailData.purchase_status != 7 && detailData.purchase_status != 8 && detailData.purchase_status != 9"></p>

        <p :class="detailData.purchase_status >= 5 && detailData.purchase_status != 6 && detailData.purchase_status != 7
        && detailData.purchase_status != 8 && detailData.purchase_status != 9 ? 'status-text on' : 'status-text'">确认入库</p>
        <p class="status-line pending" v-if="detailData.purchase_status < 5 || detailData.purchase_status == 6
        || detailData.purchase_status == 7 || detailData.purchase_status == 8 || detailData.purchase_status == 9"></p>
        <p class="status-line doing" v-if="detailData.purchase_status == 5"></p>
        <p class="status-line done" v-if="detailData.purchase_status > 5 && detailData.purchase_status != 6
        && detailData.purchase_status != 7 && detailData.purchase_status != 8 && detailData.purchase_status != 9"></p>

        <p :class="detailData.purchase_status >= 15 ? 'status-text on' : 'status-text'">完成</p>
      </div>
      <!-- 入库单 -->
      <div class="order-entry">
        <div class="entry-head">
          <p class="entry-head-title">采购入库单  {{ detailData.purchase_no }}</p>
          <p class="entry-head-status">状态：
            <span class="entry-head-status-on" v-if="detailData.purchase_status == 1">待采购单确认</span>
            <span class="entry-head-status-on" v-if="detailData.purchase_status == 4">待发货入库</span>
            <span class="entry-head-status-on" v-if="detailData.purchase_status == 5">待结算</span>
            <span class="entry-head-status-on" v-if="detailData.purchase_status == 15">已完成</span>
            <span class="entry-head-status-on" v-if="detailData.purchase_status == 9">撤销采购单</span>
          </p>
        </div>
        <div class="entry-body">
          <div class="entry-body-left">
            <div class="entry-body-num">
              <h3>采购入库单编号：</h3>
              <p>{{ detailData.purchase_no }}</p>
            </div>
            <div class="entry-body-date">
              <h3>采购单日期：</h3>
              <p>{{ detailData.creat_time }}</p>
            </div>
          </div>
          <div class="entry-body-right">
            <h3>入库地址：</h3>
            <p>{{ detailData.recipeints_address }}</p>
            <p>{{ detailData.recipeints_name }}</p>
            <p>{{ detailData.recipeints_mobilephone }}</p>
          </div>
        </div>
      </div>
      <!-- 配送方式 -->
      <div class="order-logistic" v-if="detailData.purchase_status >= 4">
        <p class="logis-title">配送方式</p>
        <p class="logis-type">配送方式：{{ detailData.logistic_method_cn }}</p>
        <div class="logis-get" v-if="detailData.logistic_method == 1">
          <p class="get-time">取货时间：{{ detailData.delivery_time }}</p>
          <p class="get-address">取货地址：{{ detailData.recipeints_address }}</p>
        </div>
        <p class="logis-send" v-if="detailData.logistic_method == 2 && detailData.delivery_time">送货时间：{{ detailData.delivery_time }}</p>
        <div class="logis-package" v-for="(packageItem, packageIndex) in detailData.package_list">
          <div class="package-item">
            <p class="package-company">包裹{{ packageIndex + 1 }}：物流公司：{{ packageItem.transport_company }}</p>
            <p class="package-no">物流单号：{{ packageItem.package_no }}</p>
          </div>
        </div>
      </div>
      <!-- 货品明细 -->
      <div class="order-detail" v-if="detailData.goods_list && detailData.goods_list.length">
        <p class="detail-title">货品明细</p>
        <table class="detail-list">
          <thead class="detail-list-title">
            <th>货品规格</th>
            <th>品牌</th>
            <th>商品条形码</th>
            <th>商家编码</th>
            <th>RFID编码</th>
            <th>供货价（HK$）</th>
            <th>数量</th>
            <th v-if="editStatus == 1">操作</th>
          </thead>
          <tr class="detail-item" v-for="(goodsItem, goodsIndex) in detailData.goods_list" v-if="goodsItem.goods_num > 0">
            <td class="detail-item-first">
              <img class="detail-item-img" :src="goodsItem.cover_image.new_path" alt="">
              <div class="detail-item-goods-info">
                <el-tooltip class="item" effect="dark" placement="top-start">
                  <div slot="content"><pre>{{goodsItem.goods_name}}</pre></div>
                  <p><pre class="detail-item-goods-name">{{ goodsItem.goods_name }}</pre></p>
                </el-tooltip>
                <p class="detail-item-goods-color">颜色：{{ goodsItem.sku_color }}</p>
                <p class="detail-item-goods-color">规格：{{ goodsItem.sku_spec }}</p>
              </div>
            </td>
            <td>{{ goodsItem.brand_name_e }}</td>
            <td class="detail-item-bar">{{ goodsItem.ofashion_bar_code }}</td>
            <td class="detail-item-sku">{{ goodsItem.ofashion_sku_code }}</td>
            <td>
              <p class="detail-rfid" v-for="rfItem in goodsItem.rfid_list"> {{ rfItem }} </p>
            </td>
            <td>{{ goodsItem.new_sku_price }}</td>
            <td>
              <span v-if="editStatus == 0">{{ goodsItem.goods_num }}</span>
              <el-input-number
                class="detail-num"
                v-if="editStatus == 1"
                size="mini"
                v-model="detailData.post_param.goods_list[goodsIndex].goods_num_new"
                :value="goodsItem.goods_num"
                :min="1"
                @change="changeGoodsNum"></el-input-number>
            </td>
            <td v-if="editStatus == 1">
              <a href="javascript:void(0)" @click="deleteGoods(goodsIndex, goodsItem.goods_num, goodsItem.id)">删除</a>
            </td>
          </tr>
        </table>
        <div class="detail-price">
          <div class="detail-price-content">
            <p class="detail-price-subtotal" v-if="detailData.new_subtotal_price"><span>小计</span><span>HK$ {{ detailData.new_subtotal_price }}</span></p>
            <p class="detail-price-total" v-if="detailData.new_total_price"><span>合计</span><span>HK$ {{ detailData.new_total_price }}</span></p>
            <p class="detail-price-capital" v-if="detailData.word_figure"><span>大写</span><span>{{ detailData.word_figure }}</span></p>
          </div>
        </div>
      </div>
      <!-- 订单操作 -->
      <div class="order-handle" v-if="editStatus == 0">
        <el-button class="handle-confirm" type="primary" @click="handleEdit"
                v-if="(detailData.purchase_status == 1 || detailData.purchase_status == 4)
                && (accountType || (order.length && (order[0]['operator_types'].includes(2)
                || order[0]['operator_types'].includes(3))))">修改采购单</el-button>
        <el-button class="handle-print" @click="toPurchaseDetail"
                   v-if="(accountType || (order.length && (order[0]['operator_types'].includes(2)
                   || order[0]['operator_types'].includes(3))))">查看入库单</el-button>
      </div>
      <div class="order-handle" v-if="editStatus == 1">
        <el-button class="handle-confirm" type="primary" @click="confirmGoodsChange">确认修改</el-button>
        <button class="handle-print" @click="handleEdit">撤销修改</button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { getOrderDetail, editOrderDetail } from '@/api/order'
  import ElInputNumber from 'element-ui/packages/input-number/src/input-number.vue'
  import OfBreadcrumb from '../../../components/breadcrumb/of-breadcrumb'
  import { MessageBox } from 'element-ui'
  import { cBrandcrumbItems } from './order-detail'

  export default {
    components: {
      ElInputNumber,
      OfBreadcrumb,
    },
    name: 'orderDetail',
    data() {
      return {
        isLoading: true,
        breadcrumb_items: cBrandcrumbItems,
        editStatus: 0,
        detailData: {},
        postParam: {},
        operatorId: '',
        purchaseNo: '',
      }
    },

    created() {
      this.purchaseNo = this.$route.query.purchase_no

      this.getDetailData(this.purchaseNo)
    },

    computed: {
      ...mapState({
        userInfo: state => state.user.userInfo,
      }),

      ...mapGetters(['accountType', 'order']),
    },

    methods: {
      getDetailData(purchaseNo) {
        getOrderDetail(purchaseNo).then(res => {
          this.detailData = res.data
          this.isLoading = false
        })
      },

      handleEdit() {
        if (this.editStatus === 0) {
          this.editStatus = 1
        } else {
          this.editStatus = 0
        }
      },

      changeGoodsNum() {
        this.postParam = this.detailData.post_param
      },

      deleteGoods(goodsIndex, goodsNum, goodsId) {
        let goodsItem = {}

        goodsItem.goods_num = goodsNum
        goodsItem.goods_num_new = 0
        goodsItem.id = goodsId
        this.postParam = this.detailData.post_param
        this.postParam.goods_list[goodsIndex] = goodsItem
        this.postParam.operator_id = this.userInfo.user_no
        MessageBox.confirm('该操作将执行删除操作, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
        }).then(() => {
          editOrderDetail(this.postParam)
            .then(res => {
              this.$message.success('操作成功')
              this.isLoading = true
              this.getDetailData(this.purchaseNo)
              this.editStatus = 0
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

      confirmGoodsChange() {
        this.postParam.operator_id = this.userInfo.user_no
        editOrderDetail(this.postParam)
          .then(res => {
            this.$message.success('操作成功')
            this.isLoading = true
            this.getDetailData(this.purchaseNo)
            this.editStatus = 0
          })
          .catch(error => {
            this.$message.error('操作失败')
          })
      },

      toPurchaseDetail() {
        this.$router.push({
          path: `/order/purchase-order-detail?purchase_no=${this.purchaseNo}`,
        })
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import 'order-detail';
</style>
