<template>
  <div class="page">
    <!--面包屑导航-->
    <el-breadcrumb class="breadcrumb">
      <el-breadcrumb-item>采购单管理</el-breadcrumb-item>
      <el-breadcrumb-item>我的采购单</el-breadcrumb-item>
      <el-breadcrumb-item>采购单详情</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="main-content page-content">
      <!-- 订单状态 -->
      <div class="order-status">
        <p :class="FundsDetailData.purchase_status >= 1 ? 'status-text on' : 'status-text'">采购入库单确认</p>
        <p class="status-line done" v-if="FundsDetailData.purchase_status > 1"></p>
        <p class="status-line doing" v-if="FundsDetailData.purchase_status == 1"></p>
        <p class="status-line pending" v-if="FundsDetailData.purchase_status < 1"></p>
        <p :class="FundsDetailData.purchase_status >= 4 ? 'status-text on' : 'status-text'">出库</p>
        <p class="status-line done" v-if="FundsDetailData.purchase_status > 4"></p>
        <p class="status-line doing" v-if="FundsDetailData.purchase_status == 4"></p>
        <p class="status-line pending" v-if="FundsDetailData.purchase_status < 4"></p>
        <p :class="FundsDetailData.purchase_status >= 5 ? 'status-text on' : 'status-text'">确认入库</p>
        <p class="status-line done" v-if="FundsDetailData.purchase_status > 5"></p>
        <p class="status-line doing" v-if="FundsDetailData.purchase_status == 5"></p>
        <p class="status-line pending" v-if="FundsDetailData.purchase_status < 5"></p>
        <p :class="FundsDetailData.purchase_status >= 15 ? 'status-text on' : 'status-text'">完成</p>
      </div>
      <!--入库地址信息-->
      <div class="buy-detail">
        <div class="buy-detail-title">
          <p class="buy-detail-size">采购入库单 {{ FundsDetailData.purchase_no }}</p>
          <p class="buy-detail-status">状态：
            <span>{{ FundsDetailData.purchase_status_text }}</span>
          </p>
        </div>
        <div class="buy-detail-info">
          <ul class="buy-detail-info-time">
            <li>
              <b>采购入库单编号:</b>
              <span>{{ FundsDetailData.purchase_no }}</span>
            </li>
            <li>
              <b>采购单日期:</b>
              <span>{{ FundsDetailData.creat_time }}</span>
            </li>
            <li>
              <b>入库日期:</b>
              <span v-if="FundsDetailData.store_time">{{ FundsDetailData.store_time }}</span>
              <span v-else>{{ FundsDetailData.cutting_sign_time }}</span>
            </li>
          </ul>
          <ul class="buy-detail-info-address">
            <li>入仓地址:</li>
            <li>{{ FundsDetailData.recipeints_address }}</li>
            <li>{{ FundsDetailData.recipeints_name }}</li>
            <li>{{ FundsDetailData.recipeints_mobilephone }}</li>
          </ul>
        </div>
      </div>

      <!-- 配送方式 -->
      <div class="order-logistic" v-if="FundsDetailData.purchase_status >= 4">
        <p class="logis-title">配送方式</p>
        <p class="logis-type">配送方式：{{ FundsDetailData.logistic_method_cn }}</p>
        <div class="logis-get" v-if="FundsDetailData.logistic_method == 1">
          <p class="get-time">取货时间：{{ FundsDetailData.delivery_time }}</p>
          <p class="get-address">取货地址：{{ FundsDetailData.recipeints_address }}</p>
        </div>
        <p class="logis-send" v-if="FundsDetailData.logistic_method == 2 && FundsDetailData.delivery_time">送货时间：{{ FundsDetailData.delivery_time }}</p>
        <div class="logis-package" v-for="(packageItem, packageIndex) in FundsDetailData.package_list">
          <div class="package-item">
            <p class="package-company">包裹{{ packageIndex + 1 }}：物流公司：{{ packageItem.transport_company }}</p>
            <p class="package-no">物流单号：{{ packageItem.package_no }}</p>
          </div>
        </div>
      </div>

      <!--table表格-->
      <div class="order-detail">
        <p class="detail-title">货品明细</p>
        <table class="detail-list">
            <thead class="detail-list-title">
              <tr>
                <th>货品规格</th>
                <th>品牌</th>
                <th>商品条形码</th>
                <th>商家编码</th>
                <th>RFID编码</th>
                <th>供货价(HKD）</th>
                <th>数量</th>
                <th>入库状态</th>
              </tr>
            </thead>
            <tr class="detail-item" v-for="(goodsItem, goodsIndex) in FundsDetailData.goods_list" v-if="goodsItem.goods_num > 0">
              <td class="detail-item-first">
                <img class="detail-item-img" :src="goodsItem.cover_image.new_path" alt="">
                <div class="detail-item-goods-info">
                  <!--<p class="detail-item-goods-name">{{ goodsItem.goods_name }}</p>-->
                  <el-tooltip class="item" effect="dark" placement="top-start">
                    <div slot="content"><pre>{{goodsItem.goods_name}}</pre></div>
                    <p><pre class="detail-item-goods-name">{{goodsItem.goods_name}}</pre></p>
                  </el-tooltip>
                  <p class="detail-item-goods-color">颜色：{{ goodsItem.sku_color }}</p>
                  <p class="detail-item-goods-color">规格：{{ goodsItem.sku_spec }}</p>
                </div>
              </td>
              <td>{{ goodsItem.brand_name_e }}</td>
              <td class="detail-item-bar">
                {{ goodsItem.ofashion_bar_code }}
              </td>
              <td class="detail-item-sku">
                {{ goodsItem.ofashion_sku_code }}
              </td>
              <td class="detail-item-rfid">
                <p class="detail-rfid" v-for="rfItem in goodsItem.rfid_list"> {{ rfItem }} </p>
              </td>
              <td>{{ goodsItem.new_sku_price }}</td>
              <td>
                <span>{{ goodsItem.goods_num }}</span>
              </td>
              <td>
                {{ goodsItem.stock_status_text }}
              </td>
            </tr>
          </table>
      </div>
    </div>
  </div>
</template>
<script>
  import { getStorageOder } from '@/api/order'

  export default {
    name: 'fillManage',
    data() {
      return {
        tableData: [
          {
            date: {
              path: 'img/1.jpg',
              name: 'yao.',
              style_color: '红色',
              style_size: '大包',
            },
            brands: 'asd',
            bar_code: 'sdgsdf',
            coding: '2016-05-02',
            rf_coding: 'asd',
            goods_price: '2334.00',
            count: '2',
          },
          {
            date: {
              path: 'img/1.jpg',
              name: 'zhou.',
              style_color: '红色',
              style_size: '小包',
            },
            brands: 'asd',
            bar_code: 'sdgsdf',
            coding: '2016-05-02',
            rf_coding: 'asd',
            goods_price: '112334.00',
            count: '2',
          },
        ],
        FundsDetailData: {},
      }
    },
    created() {
      this._getStorageOder(this.$route.query.purchase_no)
    },
    methods: {
      _getStorageOder(purchaseNo) {
        getStorageOder(purchaseNo)
          .then(res => {
            this.FundsDetailData = res.data
          })
          .catch(error => {
            this.$message.error(error)
          })
      },
    },
  }
</script>
<style lang="scss" scoped>
  @import "purchase-order-detail.scss";
</style>
