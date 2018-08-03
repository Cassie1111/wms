<template>
  <div class="page">
    <!--面包屑导航-->
    <el-breadcrumb class="breadcrumb">
      <el-breadcrumb-item>资金管理</el-breadcrumb-item>
      <el-breadcrumb-item>结算单管理</el-breadcrumb-item>
      <el-breadcrumb-item>结算单明细</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="main-content page-content">
      <!--入库地址信息-->
      <div class="buy-detail">
        <div class="buy-detail-title">
          <p class="buy-detail-size">采购结算单 {{ FundsDetailData.purchase_no }}</p>
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
              <span>{{ FundsDetailData.store_time }}</span>
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

      <!--table表格-->
      <div class="table">
        <p class="detail-title">结算明细</p>
        <template>
          <el-table
            :data="FundsDetailData.goods_list"
            style="width: 100%">
            <el-table-column
              label="货品规格"
              width="254">
              <template slot-scope="scope">
                <div class="goods_info">
                  <div class="td-left" :style="'background-image:url('+scope.row.cover_image.new_path+')'">
                  </div>
                  <div class="td-right">
                    <el-tooltip class="item" effect="dark" placement="top-start">
                      <div slot="content"><pre>{{scope.row.goods_name}}</pre></div>
                      <span><pre>{{scope.row.goods_name}}</pre></span>
                    </el-tooltip>
                    <span>颜色: {{ scope.row.sku_color }}</span>
                    <span>规格: {{ scope.row.sku_spec }}</span>
                  </div>
                </div>
              </template>
            </el-table-column>
            <el-table-column
              prop="brand_name_e"
              label="品牌"
              width="110">
            </el-table-column>
            <el-table-column
              prop="ofashion_bar_code"
              label="商品条形码"
              width='166'>
            </el-table-column>
            <el-table-column
              prop="ofashion_sku_code"
              label="商家编码"
              width="180">
            </el-table-column>
            <el-table-column
              prop="new_sku_price"
              label="供货价(HK$)"
              width="120">
            </el-table-column>
            <el-table-column
              prop="goods_num"
              label="数量"
              width='80'>
            </el-table-column>
          </el-table>
        </template>
      </div>

      <!--钱数计算-->
      <div class='bill-money'>
        <ul>
          <li><span>合计</span><span>{{ FundsDetailData.pay_currency }} {{ FundsDetailData.new_total_price }}</span></li>
          <li><span>大写</span><span>港币{{ FundsDetailData.word_figure }}</span></li>
        </ul>
      </div>
      <div class="order-handle">
        <p class="handle-confirm" v-if="accountType || funds.length > 0 && FundsDetailData.purchase_status == 5"
           @click="handleConfirm">结算完成</p>
        <a class="handle-print" v-if="accountType || funds.length > 0" href="javascript:void(0);"
           @click="toStorageOrder">查看入库单</a>
      </div>
    </div>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex'
  import { getFundDetail, setOrderStatus } from '@/api/funds/getFunds'

  export default {
    name: 'fillManage',
    data() {
      return {
        FundsDetailData: {},
      }
    },

    computed: {
      ...mapGetters(['accountType', 'funds']),
    },

    created() {
      this._getFundsDetail()
    },

    methods: {
      handleConfirm() {
        const params = {
          trade_no: this.$route.params.id,
          trade_status: this.FundsDetailData.purchase_status,

        }

        setOrderStatus(params)
          .then(res => {
            this.$message.success('确认成功')
            this._getFundsDetail()
          })
          .catch(error => {
            this.$message.error(error)
          })
      },

      toStorageOrder() {
        this.$router.push({
          path: `/order/purchase-order-detail?purchase_no=${this.$route.params.id}`,
        })
      },

      _getFundsDetail() {
        const purchase_no = this.$route.params.id

        getFundDetail(purchase_no)
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
  @import "./funds-detail.scss";
</style>
