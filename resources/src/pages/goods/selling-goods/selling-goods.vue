<template>
  <div class="page">
    <!--面包屑导航-->
    <el-breadcrumb separator="/" class="location">
      <el-breadcrumb-item>货品管理</el-breadcrumb-item>
      <el-breadcrumb-item>分销中的商品</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="main-content page-content">

      <!--顶部的搜索表单 -->
      <search-form :brandList="brandList"
                   :cateList="cateList"
                   :resultCount="total"
                   :supplierList="supplierList"
                   @search="search"
                   ref="searchForm"
                   @getBrandCate="getBrandCate"
      ></search-form>

      <!--货物展示列表 start-->
      <table class="search-result-list" v-loading="isLoading" ref="goodsList"
             element-loading-background="rgba(255,255,255,0.3)"
             element-loading-custom-class="page-loading"
             element-loading-text="加载中">
        <thead>
        <th style="width:80px;"></th>
        <th>品牌货品</th>
        <th style="width:100px;">供货价(HK$)</th>
        <th style="width:200px;">销售价(HK$)</th>
        <th style="width:100px;">库存</th>
        <th style="width:100px;">供应商</th>
        <th
          v-if="accountType ||
          sellingGoods.length && (sellingGoods[0]['operator_types'].includes(1) ||
          sellingGoods[0]['operator_types'].includes(2))"
          style="width:100px;">操作</th>
        </thead>
        <tbody>
        <tr>
          <td colspan="7">
            <div class="flex_x_between">
              <div class="check-label">
                <el-checkbox v-model="isSelectedAll" @change="selectAll"><span>全选</span></el-checkbox>
                <span>已选{{checkedCount}}个</span>
              </div>
              <div class="table-top-control">
                <el-button
                  v-if="accountType ||
                  sellingGoods.length && (sellingGoods[0]['operator_types'].includes(1) ||
                  sellingGoods[0]['operator_types'].includes(2))"
                  size="mini" @click="syncOfGoodsInfo">同步商品信息</el-button>
                <el-button
                  v-if="accountType ||
                  sellingGoods.length && (sellingGoods[0]['operator_types'].includes(1) ||
                  sellingGoods[0]['operator_types'].includes(2))"
                  size="mini" @click="unshelve">批量下架</el-button>
                <el-button
                  v-if="accountType ||
                  sellingGoods.length && (sellingGoods[0]['operator_types'].includes(1) ||
                  sellingGoods[0]['operator_types'].includes(2))"
                  size="mini" @click="openPriceDialog">批量调价</el-button>
                <el-upload
                    v-if="accountType || sellingGoods.length && sellingGoods[0]['operator_types'].includes(2)"
                    class="import"
                    :action="importUrl"
                    :on-success="handleAvatarSuccess"
                    :on-progress="uploading"
                    @click.native="getXsrfValidate"
                    :headers="uploadHeaders"
                    :show-file-list="false"
                    :multiple="false">
                  <el-button size="mini">
                    <span v-if="!importing">导入</span>
                    <span v-else>导入中...</span>
                  </el-button>
                </el-upload>
                <el-dropdown split-button type="primary" size="mini"
                    v-if="accountType || sellingGoods.length && sellingGoods[0]['operator_types'].includes(2)">
                  导出
                  <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item @click.native="exportProductList($event, 1, selectedIds)" :disabled="selectedIds.length == 0">导出已选商品</el-dropdown-item>
                    <el-dropdown-item @click.native="exportAllProductList($event, 1)">导出全部商品</el-dropdown-item>
                  </el-dropdown-menu>
                </el-dropdown>
              </div>
            </div>
          </td>
        </tr>
        <tr v-for="(item, index) in goodsList" :key="index" class="list-item">
          <td>
            <label class="click-label">
              <el-checkbox v-model="item.checked" @change="selectSingle"></el-checkbox>
            </label>
          </td>
          <td>
            <div class="flex_x_start">
              <div :style="'background-image:url('+item.cover_image[0].path_full+')'" class="list-img"></div>
              <div class="flex_y_center goods-name">
                <div>{{item.brand_name_e}}</div>
                <el-tooltip effect="dark" placement="top">
                  <div slot="content"><pre>{{item.goods_name}}</pre></div>
                  <div><pre>{{item.goods_name}}</pre></div>
                </el-tooltip>
              </div>
            </div>
            <div class="goods-no">货号：{{item.product_model}}</div>
          </td>
          <td>{{item.product_price/100 | numberFormat}}</td>
          <td>{{item.min_price/100 | numberFormat}}</td>
          <td>{{item.stock}}</td>
          <td>{{item.supplier_name || '-'}}</td>
          <td
            v-if="accountType ||
                  sellingGoods.length && (sellingGoods[0]['operator_types'].includes(1) ||
                  sellingGoods[0]['operator_types'].includes(2))"
            class="operation">
            <div @click.stop="edit($event,item)">编辑</div>
            <div @click.stop="unshelve($event,-1,item.goods_no)">下架</div>
          </td>
        </tr>
        <tr>
          <td colspan="6" v-if="noData">
            暂无数据
          </td>
        </tr>
        </tbody>
      </table>
      <!--货物展示列表 end-->

      <!--分页 start-->
      <el-pagination
          background
          layout="prev, pager, next, jumper"
          @current-change="changePage"
          :page-size= 'pageSize'
          :current-page ='page'
          v-if="total > pageSize"
          :total="total">
      </el-pagination>
      <!--分页 end -->

      <!--批量调价dialog-->
      <el-dialog
          :title="'批量调价(已选择'+checkedCount+'个商品)'"
          :visible.sync="dialogVisible"
          width="50%"
          :center="true"
          :close-on-click-modal="false"
          :show-close="false"
          class="adjust-dialog"
      >
      <span>
        <el-form label-position="right" label-width="80px">
          <el-form-item label="调价方式">
            <el-select v-model="adjustWays" placeholder="">
              <el-option
                  v-for="item in adjustWayOptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value">
              </el-option>
             </el-select>
          </el-form-item>
          <el-form-item label="调价结果">
            <el-select v-model="adjustResult" placeholder="">
              <el-option
                  v-for="item in adjustResultOptions"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value">
              </el-option>
             </el-select>
          </el-form-item>
          <el-form-item label="调价比例">
            <el-input v-model="adjustProportion" :clearable="true"></el-input>
            <span :class="{'v-hidden':adjustWays != 2}">%</span>
            <span class="tip-text">调价比例支持小数点后3位</span>
          </el-form-item>
        </el-form>
      </span>
        <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="updateGoodsPrice" size="mini" :disabled="btnDisabled">确认发布</el-button>
        <el-button @click="dialogVisible = false" size="mini">取消</el-button>
      </span>
      </el-dialog>

    </div>
  </div>
</template>

<style lang="scss" scoped>
  @import "selling-goods";
</style>
<style lang="scss">
  .page-loading{
    .el-loading-spinner{
      top:220px;
    }
  }
  .adjust-dialog {
    .el-input {
      width: 200px;
    }
  }
</style>

<script>
  import { importExcel, exportExcel } from '@/utils/excel'
  import { mapGetters } from 'vuex'
  import { BASE_API } from '@/config/env'
  import * as goodsApi from '@/api/goods/sellingGoods'
  import searchForm from './children/search-form.vue'

  const PAGE_SIZE = 50

  export default {
    name: 'selling-goods',
    data() {
      return {
        importUrl: '',
        goodsInfoList: [],
        goodsList: [],
        supplierList: [],
        supplierNo: 0,
        total: 0,
        page: 1,
        pageSize: PAGE_SIZE,
        formData: {},
        isLoading: false,
        isSelectedAll: false,
        dialogVisible: false,
        btnDisabled: false,
        importing: false,
        noData: false,
        brandList: [],
        cateList: [],
        adjustProportion: '',
        adjustResult: '2',
        adjustWays: '2',
        adjustWayOptions: [
          {
            value: '1',
            label: '数值',
          },
          {
            value: '2',
            label: '百分比',
          },
        ],
        adjustResultOptions: [
          {
            value: '1',
            label: '涨价',
          },
          {
            value: '2',
            label: '降价',
          },
        ],
        uploadHeaders: {},
      }
    },
    components: {
      searchForm,
    },
    created() {
      this._getSupplierList()
      this._getGoodsList()
    },
    mounted() {
      const importUrl = `${BASE_API}/api/goods/importGoods`

      this.importUrl = importExcel(importUrl)
    },
    filters: {
      sellStatus(status) {
        let sta = ''

        switch (status) {
          case 0: sta = '已删除'
            break
          case 1: sta = '在售中'
            break
          case -1: sta = '已下架'
            break
          case -10: sta = '缺货'
            break
          default: sta = ''
        }

        return sta
      },
    },
    computed: {
      ...mapGetters(['accountType', 'sellingGoods']),
      checkedCount() {
        return this.goodsList.filter(item => item.checked === true).length
      },
      selectedIds() {
        const ids = []

        this.goodsList.forEach(item => {
          if (item.checked === true) {
            ids.push(item.goods_no)
          }
        })

        return ids
      },
      selectedPrices() {
        const prices = []

        this.goodsList.forEach(item => {
          if (item.checked === true) {
            prices.push(item.min_price)
          }
        })

        return prices
      },
    },
    methods: {
      getXsrf(name) {
        const nameEQ = `${name}=`
        const ca = document.cookie.split(';')

        for (let i = 0; i < ca.length; i++) {
          let c = ca[i]

          while (c.charAt(0) === ' ') {
            c = c.substring(1, c.length)
          }
          if (c.indexOf(nameEQ) === 0) {
            return decodeURI(c.substring(nameEQ.length, c.length))
          }
        }

        return false
      },
      getXsrfValidate() {
        this.uploadHeaders = {
          'X-XSRF-TOKEN': decodeURIComponent(this.getXsrf('XSRF-TOKEN')),
        }
      },
      uploading() {
        this.importing = true
      },
      handleAvatarSuccess(res) {
        if (res.code === 'A500') {
          this.$message.error(res.error)
        } else if (res.code === 0) {
          this.$message.success(res.msg)
        } else if (res.code === 500 && res.msg === 'iconv(): Detected an illegal character in input string') {
          this.$message.error('请将导入文件保存后，再导入')
        } else {
          this.$message.error(res.msg)
        }
        this.importing = false
      },
      _getSupplierList() {
        goodsApi.getSupplierList().then(res => {
          this.supplierList = res.data.supplier_list
        })
      },
      _getGoodsList(supplier) {
        const query = this.$route.query
        const params = {
          start: query.page ? (query.page - 1) * PAGE_SIZE : 0,
          count: PAGE_SIZE,
        }

        this.isLoading = true
        this.getGoodsList(Object.assign(query, params))
      },
      getGoodsList(param) {
        const supplierNo = this.$refs.searchForm ? this.$refs.searchForm.supplier : ''

        param.supplier_no = supplierNo
        goodsApi.getGoodsList(param).then(res => {
          const goodsList = res.data.goods_list

          goodsList.forEach((item, index) => {
            item.checked = false
          })
          this.brandList = res.data.brands_list
          this.cateList = res.data.first_level_list
          this.total = res.data.total
          this.goodsList = goodsList
          this.isSelectedAll = false
          this.noData = res.data.goods_list.length === 0
          this.selectedIds.splice(0)

          const brandParams = {
            count: 1,
            supplier_no: supplierNo,
          }

          goodsApi.getGoodsList(brandParams).then(data => {
            this.brandList = data.data.brands_list
            this.cateList = data.data.first_level_list
          })
        }).finally(() => {
          this.isLoading = false
        })
      },

      // 选择供应商后，返回的品牌和类目需要改变
      getBrandCate(supplier) {
        const params = {
          start: 0,
          count: 1,
          supplier_no: supplier,
        }

        goodsApi.getGoodsList(params).then(res => {
          this.brandList = res.data.brands_list
          this.cateList = res.data.first_level_list
        })
      },

      // 全选
      selectAll(e) {
        if (e) {
          this.goodsList.forEach((item, index) => {
            item.checked = true
          })
        } else {
          this.goodsList.forEach((item, index) => {
            item.checked = false
          })
        }
      },

      // 单个复选框
      selectSingle() {
        this.isSelectedAll = !this.goodsList.some(item => item.checked === false)
      },
      openPriceDialog() {
        if (this.checkedCount > 0) {
          this.dialogVisible = true
        } else {
          this.$alert('选择的数量不能为0 ', '提示', {
            confirmButtonText: '确定',
          })
        }
      },

      // 更新价格
      updateGoodsPrice() {
        // 参数中，如果type = 1，表示按数值传，operate_value则表示价格，需转换成单位分
        const value = Number(this.adjustWays) === 1 ? this.adjustProportion * 100 : this.adjustProportion
        const precentReg = /^\d+(\.\d{1,3})?$/
        const intReg = /^\+?[1-9][0-9]*$/
        const params = {
          goods_no_list: this.selectedIds,
          type: Number(this.adjustWays),
          operate_mode: Number(this.adjustResult),
          operate_value: Number(value),
        }
        let errMsg = ''
        let canUpdate = true

        if (Number(this.adjustWays) === 2) {
          canUpdate = precentReg.test(value)
          errMsg = canUpdate ? '' : '调价百分比最多保留3位小数且为正数'
        }
        if (Number(this.adjustWays) === 1) {
          canUpdate = intReg.test(value / 100)
          errMsg = canUpdate ? '' : '调价数值必须为正整数'
        }
        if (typeof params.operate_value !== 'number' || this.adjustProportion === '') {
          canUpdate = false
          errMsg = '调价值不能为空且必须为数字'
        }
        if (Number(this.adjustResult) === 2 && canUpdate) {
          if (Number(this.adjustWays) === 1) {
            canUpdate = this.selectedPrices.every(item => item / 100 - value / 100 > 0)
          }
          if (Number(this.adjustWays) === 2) {
            canUpdate = this.selectedPrices.every(item => item - item * value / 100 > 0)
          }
          if (!canUpdate) {
            errMsg = '调价后的价格必须大于0'
          }
        }
        if (typeof params.operate_value === 'number' && this.adjustProportion !== '' && canUpdate) {
          this.btnDisabled = true
          goodsApi.updatePrice(params).then(res => {
            this.$message.success('修改成功')
            const param = {
              start: (this.page - 1) * PAGE_SIZE,
              count: PAGE_SIZE,
              ...this.$refs.searchForm.formData,
            }

            this.dialogVisible = false
            this.getGoodsList(param)
          }).finally(() => {
            this.btnDisabled = false
          })
        } else {
          this.$message.error(errMsg)
        }
      },

      // 下架 status = -1,上架 status = 1
      unshelve(e, status = -1, productNo = '') {
        if (this.selectedIds.length > 0 || productNo) {
          const productId = productNo ? [productNo] : this.selectedIds
          const params = {
            show_status: status,
            goods_no_list: productId,
          }

          goodsApi.unshelve(params).then(res => {
            this.$message({
              showClose: true,
              message: '操作成功',
              type: 'success',
            })

            const param = {
              start: (this.page - 1) * PAGE_SIZE,
              count: PAGE_SIZE,
              ...this.$refs.searchForm.formData,
            }

            this.isLoading = true
            this.getGoodsList(param)
          })
        } else {
          this.$alert('选择的数量不能为0 ', '提示', {
            confirmButtonText: '确定',
          })
        }
      },

      // 同步商品信息
      syncOfGoodsInfo() {
        const parmas = {
          goods_no_list: this.selectedIds,
        }

        if (this.selectedIds.length === 0) {
          this.$alert('选择的数量不能为0 ', '提示', {
            confirmButtonText: '确定',
          })
        } else {
          goodsApi.syncOfGoodsInfo(parmas).then(res => {
            this.$message({
              showClose: true,
              message: '操作成功',
              type: 'success',
            })
          })
        }
      },

      // 分页
      changePage(page) {
        const start = (page - 1) * PAGE_SIZE
        const params = {
          page,
          start,
          count: PAGE_SIZE,
          ...this.$refs.searchForm.formData,
        }

        this.page = page
        this.isLoading = true
        this.$router.push({
          path: '',
          query: {
            ...params,
          },
        })
        this.getGoodsList(params)
        window.scrollTo(0, 0)
      },

      // 编辑
      edit(e, detail) {
        const { href } = this.$router.resolve({
          name: 'goods-detail-selling',
          params: {
            goods_no: detail.goods_no,
          },
        })

        window.open(href, '_blank')
      },

      // 搜索
      search(formParams) {
        const param = {
          ...formParams,
          start: 0,
          count: PAGE_SIZE,
        }

        this.isLoading = true
        this.formData = param
        this.$router.push({
          path: '',
          query: {
            page: 1,
            ...param,
          },
        })
        this.getGoodsList(param)
      },

      // 导出
      exportProductList(e, status = 1, productNoList = this.selectedIds) {
        if (productNoList.length > 0) {
          const exportUrl = `${BASE_API}/api/goods/exportProductList?product_no_list=${productNoList}`

          exportExcel(exportUrl)
        } else {
          this.$alert('选择的数量不能为0 ', '提示', {
            confirmButtonText: '确定',
          })
        }
      },

      // 导出全部
      exportAllProductList(e, status = 1) {
        const params = {
          start: 0,
          count: this.total,
          status,
          ...this.$refs.searchForm.formData,
        }
        const exportBaseUrl = `${BASE_API}/api/goods/exportAllProductList?`
        let query = ''

        for (let key in params) {
          query += `${key}=${params[key]}&`
        }
        query = query.substr(0, query.length - 1)

        const exportUrl = exportBaseUrl + query

        exportExcel(exportUrl)
      },
    },
  }
</script>

