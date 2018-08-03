<template>
  <div class="page">
    <el-breadcrumb separator="/" class="location">
      <el-breadcrumb-item>货品管理</el-breadcrumb-item>
      <el-breadcrumb-item v-if="!isEdit">创建新商品</el-breadcrumb-item>
      <el-breadcrumb-item v-else>编辑商品</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="main-content page-content detail-page" v-loading="pageLoading"
         element-loading-custom-class="page-loading"
         element-loading-background="rgba(255,255,255,1)"
         element-loading-text="加载中"
    >
      <div class="bread-nav">已选类目：{{selectedCate}} <span class="edit-type" v-if="!isEdit" @click="changeType">修改</span></div>
      <div class="content-header">
        1. 基本信息
      </div>

      <!-- 表单主体 start-->
      <el-dialog
          title="选择品牌"
          :visible.sync="brandDialogVisible"
          width="1200px"
          :close-on-click-modal="false"
          :show-close="true"
          :lock-scroll="true"
          custom-class = "brand-dialog"
      >
        <brand-dialog :brandList="brandList" @selectBrand="selectBrand"></brand-dialog>
      </el-dialog>

      <div class="ware-detail-table">
        <div class="table-row">
          <div class="row-label is-required">品牌</div>
          <div class="row-content">
            <el-input placeholder="填写品牌" v-model="brandInfo.brand_name_e"
                      @focus="openBrandDialog"
                      :disabled="!brandEditable"
            >

            </el-input>
          </div>
        </div>

        <!--供应商字段暂时不需要-->
        <div class="table-row" v-if="0">
          <div class="row-label is-required">供应商</div>
          <div class="row-content">
            <el-select  v-model="supplier" :disabled="isEdit" :clearable="true" class="supplier-select">
              <el-option
                  v-for="option in supplierList"
                  :key="option.supplier_no"
                  :label="option.supplier_no +' / '+ option.supplier_name"
                  :value="option.supplier_no"
              >
              </el-option>
            </el-select>
          </div>
        </div>

        <div class="table-row">
          <div class="row-label is-required">标题</div>
          <div>
            <el-input class="ware-title" v-model.trim="title"
                      size = "mini"
                      placeholder="请填写标题"
                      name = ""
                      maxlength= "60"
            ></el-input>
          </div>
          <div class="words-count">{{currentLen}}/30</div>
        </div>
      </div>
      <!--商品属性-->
      <goods-property :goodsDetail="goodsDetail"
                      :propertyInfo="quoteInfo.property_info"
                      :isEdit="isEdit"
                      ref="goodsProperty"
                      :productDetail="productDetail"
                      :changeType="changeTypeFlag"
                      v-if="requestFinished"
      ></goods-property>
      <!--商品规格-->
      <goods-spec :quoteInfo="quoteInfo"
                  :goodsDetail="goodsDetail"
                  :productDetail="productDetail"
                  :changeType="changeTypeFlag"
                  :isEdit="isEdit"
                  v-if="requestFinished"
                  ref="goodsSpec"
      ></goods-spec>
      <!--商品细节图和主图-->
      <goods-images
          :coverImage="goodsDetail.cover_image"
          :detailImage="goodsDetail.detail_image"
          :productDetail="productDetail"
          :changeType="changeTypeFlag"
          :isEdit="isEdit"
          ref="goodsImages"
          v-if="requestFinished"
      ></goods-images>

      <!--商品描述-->
      <div class="table-row">
        <div class="row-label">商品描述</div>
        <div class="row-content">
          <el-input
              type="textarea"
              :rows="6"
              placeholder="请输入内容"
              v-model="description"
          >
          </el-input>
        </div>
      </div>

      <div class="content-header">
        2. 物流服务
      </div>

      <div class="ware-detail-table">
        <div class="table-row">
          <div class="row-label">配送物流</div>
          <div class="row-content row-content-bg">
            <div class="logistics-info-item">
              <el-checkbox v-model="isPickSelf">自提</el-checkbox>
              <el-input disabled></el-input>
            </div>

            <div class="logistics-info-item">
              <el-checkbox v-model="isLogistics">一个地址</el-checkbox>
              <el-input placeholder="物流模式费用" v-model="logisticsRate" disabled></el-input>
            </div>

            <div class="logistics-info-item">
              <el-checkbox v-model="isSubstituting">多个地址</el-checkbox>
              <el-input placeholder="代发费用" v-model="substitutingRate" disabled></el-input>
            </div>
          </div>
        </div>
      </div>

      <!-- 表单主体 end -->
      <div class="of_flex_x_center">
        <el-button type="primary" size="mini" @click="publish" class="btn-publish">确认发布</el-button>
        <!--<el-button  size="mini">保存草稿</el-button>-->
      </div>
    </div>
  </div>
</template>

<script>
  import * as detailApi from '@/api/goods/goodsDetail'
  import { getSupplierList } from '@/api/goods/sellingGoods'
  import { mapState } from 'vuex'
  import validate from './validate'
  import goodsProperty from './children/goods-property.vue'
  import goodsSpec from './children/goods-spec.vue'
  import goodsImages from './children/goods-images.vue'
  import brandDialog from './children/brand-dialog.vue'

  export default {
    name: 'goods-detail',
    data() {
      return {
        pageLoading: true,
        brandInfo: {},
        goodsDetail: {},
        productDetail: {},
        brandList: [],
        files: [],
        supplier: '',
        supplierList: [],
        title: '',
        description: '',
        goodsCate: '',
        checked: false,
        brandDialogVisible: false,
        colorDialogVisible: false,
        requestFinished: false,
        quoteInfo: {},
        color: [],
        checkList: [],
        colorIndex: 0,
        isPickSelf: false,
        isLogistics: false,
        isSubstituting: false,
        substitutingRate: null,
        logisticsRate: null,
      }
    },
    components: {
      goodsProperty,
      goodsSpec,
      goodsImages,
      brandDialog,
    },
    computed: {
      currentLen() {
        let len = 0

        for (let i = 0; i < this.title.length; i++) {
          const c = this.title.charCodeAt(i)

          if ((c >= 0x0001 && c <= 0x007e) || (c >= 0xff60 && c <= 0xff9f)) {
            len += 1
          } else {
            len += 2
          }
        }
        if (len > 60) {
          this.overMaxLen = true
        } else {
          this.overMaxLen = false
        }

        return Math.floor(len / 2)
      },
      selectedCate() {
        let str = ''

        if (this.isEdit) {
          const cate = [...this.goodsCate]

          cate.forEach(item => {
            str += `${item.value}>`
          })
          str = str.substring(0, str.length - 1)
        } else {
          str = decodeURIComponent(this.$route.query.cate_value).replace(/,/g, '>')
        }

        return str
      },
      isEdit() {
        const path = this.$route.path.split('/')

        return path[path.length - 1] !== 'create'
      },
      brandEditable() {
        return (this.goodsDetail && this.goodsDetail.goods_brand_edit_enable) || !this.isEdit
      },
      ...mapState({
        userInfo: state => state.user.userInfo,
      }),
    },
    created() {
      const path = this.$route.path.split('/')

      // changeType是修改类目的标记
      this.changeTypeFlag = this.$route.query.changeType
      if (path[path.length - 1] !== 'create') {
        this._getGoodsDetail()
      } else {
        this._getNewQuoteInfo()
      }
    },
    methods: {
      _getGoodsDetail() {
        const goodsNo = this.$route.params.goods_no || ''
        const status = this.$route.params.status

        const params = {
          goods_no: goodsNo,
          status,
        }

        detailApi.getGoodsDetail(params).then(res => {
          const GoodsTree = res.data.goods_type

          this.goodsDetail = res.data
          this.goodsCate = this.formatCateInfo(GoodsTree)
          this.title = res.data.goods_name
          this.brandInfo.brand_name_e = res.data.brand_name_e
          this.brandInfo.brand_name_c = res.data.brand_name_c
          this.brandInfo.brand_id = res.data.brand_id
          this.description = res.data.description
          this.supplier = res.data.supplier_name
          this.isPickSelf = Boolean(res.data.is_pick_up_by_customer)
          this.isLogistics = Boolean(res.data.is_logistics)
          this.isSubstituting = Boolean(res.data.is_substituting)
          this.substitutingRate = res.data.substituting_shipping_rate / 100
          this.logisticsRate = res.data.logistics_shipping_rate / 100

          return Promise.resolve({
            cate_id: res.data.final_level_cate_id,
            virtual_cate_id: res.data.virtual_cate_id,
          })
        }).then(res => {
          const skuParams = {
            cate_id: res.cate_id,
            virtual_cate_id: res.virtual_cate_id,
            sku_property: 0,
            code_id: 0,
          }

          detailApi.getQuoteInfoByCate(skuParams).then(info => {
            this.quoteInfo = info.data
            this.pageLoading = false
            this.requestFinished = true
          })
        })
      },

      // 新建的方式进入页面
      _getNewQuoteInfo() {
        const skuParams = {
          cate_id: this.$route.query.cate_id,
          virtual_cate_id: this.$route.query.virtual_cate_id,
          sku_property: 0,
          code_id: 0,
        }

        getSupplierList().then(res => {
          this.supplierList = res.data.supplier_list
        })
        detailApi.getQuoteInfoByCate(skuParams).then(info => {
          this.quoteInfo = info.data
          this.pageLoading = false
          this.requestFinished = true

          // 如果是修改类目之后返回新建页面，获取上次用户输入的缓存渲染到页面上
          if (this.changeTypeFlag) {
            this.renderLastInput()
          }
        })
      },

      renderLastInput() {
        const data = JSON.parse(localStorage.getItem('productDetail'))

        this.productDetail = data
        this.title = data.goods_name
        this.brandInfo.brand_name_e = data.brand_name_e
        this.brandInfo.brand_name_c = data.brand_name_c
        this.brandInfo.brand_id = data.brand_id
        this.description = data.description
        this.supplier = data.supplier_name
      },

      // 点击表单元素-品牌
      selectBrand(item) {
        this.brandInfo.brand_name_e = item.name_e
        this.brandInfo.brand_name_c = item.name_c
        this.brandInfo.brand_id = item.brand_id
        this.brandDialogVisible = false
      },
      openBrandDialog() {
        this.brandDialogVisible = true
      },

      // 格式化选择类型的树形结构
      formatCateInfo(obj) {
        if (!obj) return ''
        const res = []

        res.push({
          value: obj.type_name,
          id: obj.id,
        })
        for (const k in obj) {
          if (k === 'child_type_info' && obj[k].type_name) {
            res.push(this.formatCateInfo(obj[k]))
          }
        }

        return this.flatten(res)
      },
      flatten(arr) {
        while (arr.some(item => Array.isArray(item))) {
          // eslint-disable-next-line
          arr = [].concat(...arr)
        }

        return arr
      },
      changeType() {
        // 缓存用户已经填写的数据
        this.$confirm('修改类目可能会导致部分选中的规格不被保存, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning',
        }).then(() => {
          const cache = this.buildParams()

          cache.productSku = this.$refs.goodsSpec.productSku
          cache.colorList = this.$refs.goodsSpec.colorList
          cache.specList = this.$refs.goodsSpec.specList
          cache.specSelfList = this.$refs.goodsSpec.specSelfList
          cache.specSubList = this.$refs.goodsSpec.specSubList
          cache.goodsSpecCodeId = this.$refs.goodsSpec.goodsSpecCodeId
          cache.specRadio = this.$refs.goodsSpec.specRadio

          localStorage.setItem('productDetail', JSON.stringify(cache))
          this.$router.push({
            name: 'create-goods',
            params: {
              changeType: true,
            },
          })
        }).catch(() => {
          // ...
        })
      },
      // eslint-disable-next-line
      buildParams() {
        // eslint-disable-next-line
        let property = this.$refs.goodsProperty.property
        const productSku = this.$refs.goodsSpec.productSku
        const coverImage = this.$refs.goodsImages.coverList
        const detailImage = this.$refs.goodsImages.detailList
        const currencyInfo = this.$refs.goodsSpec.currencyInfo
        const allPrice = []
        const allProductPrice = []
        const goodsProperty = []
        let stock = 0

        property.forEach((item, index) => {
          if (index === 0) {
            goodsProperty.push(item)
          } else if (item.value_list[0] && item.value_list[0].property_value !== '') {
            goodsProperty.push(item)
          }
        })

        productSku.forEach(item => {
          if (typeof Number(item.sku_stock) === 'number') {
            stock += Number(item.sku_stock)
          }
          if (!item.sku_image_list) {
            item.sku_image_list = []
          }
          if (typeof Number(item.sku_price) === 'number' && Number(item.sku_price) !== 0) {
            allPrice.push(item.sku_price)
          }
          if (typeof Number(item.product_sku_price) === 'number' && Number(item.product_sku_price) !== 0) {
            allProductPrice.push(item.product_sku_price)
          }

          // 这些默认的属性可能是页面默认加载的时候product_sku里面的成员对象自带的，提交的时候需要把他们删除，否则接口会报错
          delete item.checked
          delete item.sku_color_id
          delete item.sku_size_id
          delete item.sku_id
          delete item.goods_no
          delete item.sku_status
        })

        const filterSku = productSku.filter(item => {
          const emptyPrice = item.product_sku_price === ''
          const emptyStock = item.sku_stock === ''

          return !emptyStock && !emptyPrice
        })
        const price = allPrice.length === 0 ? 0 : Math.min.apply(null, allPrice)
        const productPrice = allProductPrice.length === 0 ? 0 : Math.min.apply(null, allProductPrice)
        const status = this.$route.params.status
        const formParams = {
          status,
          goods_name: this.title,
          goods_no: this.goodsDetail.goods_no,
          brand_id: this.brandInfo.brand_id,
          brand_name_e: this.brandInfo.brand_name_e,
          brand_name_c: this.brandInfo.brand_name_c,
          goods_spec_code_id: this.$refs.goodsSpec.goodsSpecCodeId || 0,
          product_property: goodsProperty,
          description: this.description,
          currency: this.$refs.goodsSpec.currency,
          stock,
          product_price: productPrice,
          official_price: this.$refs.goodsSpec.officialPrice,
          final_level_cate_id: this.goodsDetail.final_level_cate_id || this.$route.query.cate_id,
          final_level_cate_name: this.goodsDetail.final_level_cate_name || this.$route.query.cate_name,
          first_level_cate_id: this.goodsDetail.first_level_cate_id || this.$route.query.first_cate_id,
          first_level_cate_name: this.goodsDetail.first_level_cate_name || this.$route.query.first_cate_name,
          virtual_cate_id: this.goodsDetail.virtual_cate_id || this.goodsDetail.virtual_cate_id === 0 ? this.goodsDetail.virtual_cate_id : this.$route.query.virtual_cate_id,
          show_status: this.show_status || 1,
          channel_source: this.goodsDetail.channel_source || 1,
          product_sku: filterSku,
          cover_image: coverImage,
          detail_image: detailImage,
          ofashion_bar_code: this.$refs.goodsSpec.barCode,
          is_pick_up_by_customer: this.isPickSelf,
          is_logistics: this.isLogistics,
          is_substituting: this.isSubstituting,
          substituting_shipping_rate: this.substitutingRate,
          logistics_shipping_rate: this.logisticsRate,
        }

        console.log(formParams)

        return formParams
      },
      publish() {
        const formParams = this.buildParams()
        let canSubmit = false

        canSubmit = validate(this, formParams)
        if (this.overMaxLen) {
          this.$alert('标题长度超过限制', '提示', {
            confirmButtonText: '确定',
          })

          canSubmit = false
        }
        if (canSubmit) {
          detailApi.editProductDetail(formParams).then(res => {
            this.$message.success('保存成功')

            this.$router.push('/goods/selling-goods')
          })
        }
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import "goods-detail";
</style>

<style lang="scss">
  @import "goods-detail-g";
</style>
