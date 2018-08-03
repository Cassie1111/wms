<template>
  <form class="form-search">
    <div class="form-search-list">
      <div class="form-item">
        <label>供应商</label>
        <el-select v-model="supplier" placeholder="" :title="supplier" @change="getBrandCate" :clearable=true>
          <el-option
              v-for="item in supplierList"
              :key="item.id"
              :label="item.supplier_name"
              :value="item.supplier_no">
          </el-option>
        </el-select>
      </div>

      <div class="form-item flex-start">
        <label>品牌</label>
        <el-select v-model="brandId" placeholder=""  filterable :clearable=true>
          <el-option
              v-for="item in brandList"
              :key="item.brand_id"
              :label="item.brand_name_e"
              :value="item.brand_id">
          </el-option>
        </el-select>
      </div>

      <div class="form-item flex-start">
        <label>品类</label>
        <el-select v-model="cateId" placeholder="" :clearable=true>
          <el-option
              v-for="item in cateList"
              :key="item.first_level_cate_id"
              :label="item.first_level_cate_name"
              :value="item.first_level_cate_id">
          </el-option>
        </el-select>
      </div>
    </div>

    <div class="form-search-list">
      <div class="form-item">
        <label>品牌货号</label>
        <el-input v-model="brandNo" placeholder="" :clearable=true></el-input>
      </div>

      <div class="form-item flex-start">
        <label>编码</label>
        <el-input v-model="code" placeholder="" :clearable=true></el-input>
      </div>

      <div class="form-item flex-start">
        <label>名称</label>
        <el-input v-model="productName" placeholder="" :clearable=true></el-input>
      </div>
    </div>

    <div class="form-search-list">
      <div class="full-width">
        <el-button type="primary"
                   icon="el-icon-search"
                   size="mini"
                   @click="search"
                   class="btn-search"
        >搜索</el-button>
        <span class="search-count">共搜索到{{resultCount}}个商品</span>
      </div>
    </div>
  </form>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    data() {
      return {
        brandId: '',
        cateId: '',
        brandNo: '',
        code: '',
        productName: '',
        supplier: '',
      }
    },
    props: {
      brandList: {
        type: Array,
        'default': () => [],
      },
      cateList: {
        type: Array,
        'default': () => [],
      },
      resultCount: {
        type: Number,
        'default': 0,
      },
      supplierList: {
        type: Array,
        'default': () => [],
      },
    },
    computed: {
      formData() {
        return {
          brand_id: this.brandId,
          cate_id: this.cateId,
          brand_no: this.brandNo,
          code: this.code,
          goods_name: this.productName,
          supplier_no: this.supplier || '',
        }
      },
      ...mapState({
        userInfo: state => state.user.userInfo,
      }),
    },
    methods: {
      search() {
        const formData = this.formData

        this.$emit('search', formData)
      },
      getBrandCate() {
        this.brandId = ''
        this.cateId = ''
        this.$emit('getBrandCate', this.supplier)
      },
    },
  }
</script>

<style lang="scss">
  @import "search-form";
</style>
