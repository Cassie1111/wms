<template>
  <div class="page">
    <!--面包屑导航-->
    <of-brandcrumb :items="brandcrumb_items"></of-brandcrumb>

    <div class="main-content">
      <!--搜索功能-->
      <div class="content-input">
        <div class="content-input-list first">
          <p class="content-input-item">
            <span>订单编号</span>
            <el-input size="mini" class="input-item" v-model="tradeNoVal" type="text"></el-input>
          </p>
          <p class="content-input-item">
            <span>货号</span>
            <el-input size="mini" class="input-item" v-model="modelVal" type="text"></el-input>
          </p>
          <p class="content-input-item">
            <span>名称</span>
            <el-input size="mini" class="input-item" v-model="goodsNameVal" type="text"></el-input>
          </p>
        </div>
        <div class="content-input-list second">
          <p class="content-input-item">
            <span class="mini">状态</span>
            <el-select size="mini" v-model="refundStatusVal" class="select-status" clearable>
              <el-option
                v-for="statusItem in refundStatus"
                :key="statusItem.value"
                :label="statusItem.label"
                :value="statusItem.value">
              </el-option>
            </el-select>
          </p>
          <p class="content-input-item">
            <span class="mini">编码</span>
            <el-input size="mini" class="input-item" v-model="codeVal" type="text"></el-input></p>
          <el-button class="search" type="primary" @click="searchData">
            搜索
          </el-button>
          <span class="search-amount">共搜索到{{ total }}个退款单</span>
        </div>
        <div class="content-input-list third">
          <p class="content-input-item">
            <span>提货方式</span>
            <el-select size="mini" v-model="logisticMethodVal" class="select-status" clearable>
              <el-option
                v-for="methodItem in logisticMethod"
                :key="methodItem.value"
                :label="methodItem.label"
                :value="methodItem.value">
              </el-option>
            </el-select>
          </p>
          <p class="content-input-item">
            <span>时间</span>
            <el-date-picker
              class="input-time"
              size="mini"
              v-model="dateVal"
              type="daterange"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              :format="'yyyy-MM-dd'"
              :value-format="'yyyy-MM-dd'"
              align="right"
              @change="dateSelect">
            </el-date-picker>
          </p>
        </div>
      </div>

      <div class="refund-table">
        <!--tab切换-->
        <div class="refund-table-tab" type="card">
          <p :class="refundStatusVal == '1,2,3' ? 'table-tab-item on':'table-tab-item'"
             data-aid="1,2,3"
             @click="tabChange">全部</p>
          <p :class="refundStatusVal == '1' ? 'table-tab-item on':'table-tab-item'"
             data-aid="1"
             @click="tabChange">待退款</p>
          <p :class="refundStatusVal == '2' ? 'table-tab-item on':'table-tab-item'"
             data-aid="2"
             @click="tabChange">退款成功</p>
        </div>

        <div class="refund-table-content">
          <table class="table-list" border="0" cellspacing="0" cellpadding="0" v-loading="isLoading">
            <tr class="table-head">
              <td style="width: 270px;">货品</td>
              <td>单价(HK$)</td>
              <td>数量</td>
              <td>总金额(HK$)</td>
              <td>状态</td>
              <td>操作</td>
            </tr>
            <tr>
              <td colspan="6">
                <div class="export">
                  <el-dropdown
                    split-button
                    type="primary"
                    size="small"
                    trigger="click"
                    @command="handleExport"
                    v-if="(listData && listData.length)
                    && (accountType || refund.length > 0)">
                    <span class="el-dropdown-link">
                      导出退款单
                    </span>
                    <el-dropdown-menu slot="dropdown">
                      <el-dropdown-item command="all">全部导出</el-dropdown-item>
                      <el-dropdown-item command="checked" :disabled="this.checkedList.length == 0">导出选中</el-dropdown-item>
                    </el-dropdown-menu>
                  </el-dropdown>
                </div>
              </td>
            </tr>

            <tbody class="table-tbody" v-if="listData.length">
              <template v-for="refundItem in listData">
                <tr class="table-info-wrap">
                  <td colspan="6">
                    <div class="table-info">
                      <div class="table-no">
                        <el-checkbox @change="checkOrder" :id="refundItem.trade_no">
                          订单编号： <span>{{ refundItem.trade_no }}</span>
                        </el-checkbox>
                      </div>
                      <div class="table-time">
                        下单时间：<span>{{ refundItem.create_time }}</span>
                      </div>
                    </div>
                  </td>
                </tr>
                <tr v-for="(goodsItem, goodsIndex) in refundItem.trade_detail">
                  <td>
                    <div class="table-goods">
                      <div class="table-goods-img" :style="{backgroundImage:'url('+goodsItem.cover_image.new_path+')'}"></div>
                      <div class="table-goods-info">
                        <el-tooltip class="item" effect="dark" placement="top-start">
                          <div slot="content"><pre>{{goodsItem.goods_name}}</pre></div>
                          <p><pre class="table-goods-name">{{goodsItem.goods_name}}</pre></p>
                        </el-tooltip>
                        <p class="table-goods-color">颜色: {{ goodsItem.sku_color }}</p>
                        <p class="table-goods-spec">规格: {{ goodsItem.sku_spec }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="tbody-item">{{ goodsItem.new_sku_price }}</td>
                  <td class="tbody-item">{{ goodsItem.goods_sku_num }}</td>
                  <td class="tbody-item" v-if="refundItem.trade_detail.length == 1">{{ refundItem.new_pay_price }}</td>
                  <td class="tbody-item" v-else-if="goodsIndex == 0" :rowspan="refundItem.trade_detail.length">{{ refundItem.new_pay_price }}</td>
                  <td class="tbody-item" v-if="refundItem.trade_detail.length == 1">{{ refundItem.refund_status_cn }}</td>
                  <td class="tbody-item" v-else-if="goodsIndex == 0" :rowspan="refundItem.trade_detail.length">
                    {{ refundItem.refund_status_cn }}
                  </td>
                  <td class="tbody-item" v-if="refundItem.trade_detail.length == 1">
                    <el-button size="mini" class="tbody-btn" type="primary"
                               v-if="refundItem.refund_status == 1
                               && (accountType || (refund.length > 0 && refund[0]['operator_types'].includes(1)))"
                               @click="refundOrder(refundItem.trade_no, refundItem.pay_price)">退款</el-button>
                  </td>
                  <td class="tbody-item" v-else-if="goodsIndex == 0" :rowspan="refundItem.trade_detail.length">
                    <el-button size="mini" class="tbody-btn" type="primary"
                               v-if="refundItem.refund_status == 1
                               && (accountType || (refund.length > 0 && refund[0]['operator_types'].includes(1)))"
                               @click="refundOrder(refundItem.trade_no, refundItem.pay_price)">退款</el-button>
                  </td>
                </tr>
              </template>
            </tbody>
            <tr v-else>
              <td colspan="6" class="table-no-data">暂无数据</td>
            </tr>
          </table>

          <el-pagination
            background
            @current-change="handlePageChange"
            :current-page.sync="page"
            :page-size="20"
            layout="total, prev, pager, next, jumper"
            :total="total"
            v-if="total && total > 20">
          </el-pagination>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import { mapState, mapGetters } from 'vuex'
  import { getRefundList, refundOrder } from '@/api/order'
  import { exportExcel } from '@/utils/excel'
  import { param } from '@/utils/param'
  import { MessageBox } from 'element-ui'
  import OfBrandcrumb from '../../../components/breadcrumb/of-breadcrumb'

  export default {
    components: {
      OfBrandcrumb,
    },
    name: 'refund',
    data() {
      return {
        isLoading: true,
        brandcrumb_items: [
          {
            title: '订单管理',
          },
          {
            title: '退款管理',
          },
        ],
        refundStatusVal: '1,2,3',
        tradeNoVal: '',
        logisticMethodVal: '',
        goodsNameVal: '',
        codeVal: '',
        modelVal: '',
        dateVal: '',
        refundStatus: [
          {
            value: '1,2,3',
            label: '全部',
          },
          {
            value: '1',
            label: '待退款',
          },
          {
            value: '2',
            label: '退款成功',
          },
        ],
        logisticMethod: [
          {
            value: '1',
            label: '自提',
          },
          {
            value: '2',
            label: '单个地址发货',
          },
          {
            value: '3',
            label: '多个地址发货',
          },
        ],
        total: '',
        listData: [],
        checkedList: [],
        page: 1,
      }
    },
    created() {
      const params = {}

      this.getListData(params)
    },
    computed: {
      ...mapState({
        userInfo: state => state.user.userInfo,
      }),

      ...mapGetters(['accountType', 'refund']),
    },
    methods: {
      getListData(params) {
        getRefundList(params).then(res => {
          this.listData = res.data.refundList.trade_list
          this.total = res.data.refundList.total
          this.isLoading = false
        })
      },

      tabChange(e) {
        this.isLoading = true

        const refundStatus = e.currentTarget.getAttribute('data-aid')
        const tradeNo = this.tradeNoVal
        const logisticMethod = this.logisticMethodVal
        const goodsName = this.goodsNameVal
        const code = this.codeVal
        const productModel = this.modelVal
        const startTime = this.dateVal[0]
        const endTime = this.dateVal[1]
        const params = {
          refund_status: refundStatus,
          trade_no: tradeNo,
          logistic_method: logisticMethod,
          goods_name: goodsName,
          code,
          product_model: productModel,
          start_time: startTime,
          end_time: endTime,
        }

        this.refundStatusVal = refundStatus
        this.refundStatusVal = refundStatus
        this.getListData(params)
      },

      dateSelect(val) {
        this.dateVal = val
      },

      searchData() {
        this.isLoading = true

        const refundStatus = this.refundStatusVal
        const tradeNo = this.tradeNoVal
        const logisticMethod = this.logisticMethodVal
        const goodsName = this.goodsNameVal
        const code = this.codeVal
        const productModel = this.modelVal
        const startTime = this.dateVal[0]
        const endTime = this.dateVal[1]
        const searchParam = {
          refund_status: refundStatus,
          trade_no: tradeNo,
          logistic_method: logisticMethod,
          goods_name: goodsName,
          code,
          product_model: productModel,
          start_time: startTime,
          end_time: endTime,
        }

        this.getListData(searchParam)
      },

      handlePageChange(val) {
        this.isLoading = true

        const refundStatus = this.refundStatusVal
        const tradeNo = this.tradeNoVal
        const logisticMethod = this.logisticMethodVal
        const goodsName = this.goodsNameVal
        const code = this.codeVal
        const productModel = this.modelVal
        const startTime = this.dateVal[0]
        const endTime = this.dateVal[1]
        const page = val
        const params = {
          refund_status: refundStatus,
          trade_no: tradeNo,
          logistic_method: logisticMethod,
          goods_name: goodsName,
          code,
          product_model: productModel,
          start_time: startTime,
          end_time: endTime,
          page,
        }

        this.getListData(params)
      },

      checkOrder(val, e) {
        const tradeNo = e.target.labels[0].id

        if (val) {
          this.checkedList.push(tradeNo)
        } else {
          this.checkedList.splice(this.checkedList.indexOf(tradeNo), 1)
        }
      },

      exportAll() {
        const refundStatus = this.refundStatusVal
        const tradeNo = this.tradeNoVal
        const logisticMethod = this.logisticMethodVal
        const goodsName = this.goodsNameVal
        const code = this.codeVal
        const productModel = this.modelVal
        const startTime = this.dateVal[0]
        const endTime = this.dateVal[1]
        const exportParams = {
          refund_status: refundStatus,
          trade_no: tradeNo,
          logistic_method: logisticMethod,
          goods_name: goodsName,
          code,
          product_model: productModel,
          start_time: startTime,
          end_time: endTime,
        }

        let originUrl = `${window.location.origin}/api/order/exportExcel`
        let exportUrl = `${originUrl}${originUrl.indexOf('?') == -1 ? '?' : '&'}${param(exportParams)}`

        exportExcel(exportUrl)
      },

      exportChecked() {
        const refundStatus = this.refundStatusVal
        const tradeNo = this.tradeNoVal
        const logisticMethod = this.logisticMethodVal
        const goodsName = this.goodsNameVal
        const code = this.codeVal
        const productModel = this.modelVal
        const startTime = this.dateVal[0]
        const endTime = this.dateVal[1]
        const checkList = this.checkedList
        const exportParams = {
          refund_status: refundStatus,
          trade_no: tradeNo,
          logistic_method: logisticMethod,
          goods_name: goodsName,
          code,
          product_model: productModel,
          start_time: startTime,
          end_time: endTime,
          check_list: checkList,
        }

        if (!checkList.length) {
          this.$message('请选择需要导出的采购单')

          return false
        }

        const originUrl = `${window.location.origin}/api/order/exportExcel`
        const exportUrl = `${originUrl}${originUrl.indexOf('?') == -1 ? '?' : '&'}${param(exportParams)}`

        exportExcel(exportUrl)
      },

      handleExport(command) {
        if (command == 'all') {
          this.exportAll()
        } else if (command == 'checked') {
          this.exportChecked()
        } else {
          return false
        }
      },

      refundOrder(tradeNo, payPrice) {
        const params = {
          trade_no: tradeNo,
          operator_id: this.userInfo.user_no,
          refund_fee: payPrice,
        }

        MessageBox.confirm('该操作将执行退款操作, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
        }).then(() => {
          refundOrder(params)
            .then(res => {
              this.$message.success('操作成功')
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
    },
  }
</script>

<style lang="scss" scoped>
  @import "refund.scss";
</style>
