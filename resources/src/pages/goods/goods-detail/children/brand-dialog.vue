<template>
  <div>
  <slot name="title">
    <div class="search flex">
      <el-input placeholder="请输入品牌关键字" v-model="keywords"></el-input>
      <el-button size="mini" type="primary" @click="search" icon="el-icon-search">搜索</el-button>
    </div>
    <div class="tab">
      <ul class="of_flex tab-letter">
        <li class="tab-item"
        v-for="(item,index) in tabs"
        :key="index"
        @click="selectTab(item,index)"
        :class = "clickedIndex == index ? 'active' : ''"
        >{{item}}</li>
      </ul>
    </div>
    <div class="tab-content" v-loading="loading" element-loading-background="rgba(255,255,255,1)">
      <div class="brand-item" v-for="item in brandList"
           :title="item.name_c+'/'+item.name_e"
           @click="selectBrand(item)"
      >
        {{item.name_e}}/{{item.name_c}}
      </div>
      <div v-if="brandList.length === 0" class="no-result">
        暂无搜索结果
      </div>
    </div>
  </slot>
  </div>
</template>
<script>
  import { getBrands } from '@/api/goods/goodsDetail'

  const ALL_BRAND = 0
  const OTHER_BRAND = 27

  export default {
    name: 'brand-dialog',
    data() {
      return {
        tabs: [
          '所有品牌',
          ...'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
          '其他',
        ],
        clickedIndex: 0,
        brandList: [],
        brandListCopy: [],
        loading: true,
        keywords: '',
      }
    },
    created() {
      getBrands().then(res => {
        this.brandList = res.data.brand_list
        this.brandListCopy = res.data.brand_list
        this.loading = false
      })
    },
    methods: {
      selectTab(item, index) {
        const brandList = [...this.brandListCopy]
        const filter = brandList.filter(brand => brand.name_e[0].toUpperCase() === item)
        const other = brandList.filter(brand => !new RegExp(/[A-Z]/).test(brand.name_e[0].toUpperCase()))

        this.clickedIndex = index
        if (index === ALL_BRAND) {
          this.brandList = brandList
        } else if (index === OTHER_BRAND) {
          this.brandList = other
        } else {
          this.brandList = filter
        }
      },
      selectBrand(item) {
        this.$emit('selectBrand', item)
      },
      search() {
        const brandList = [...this.brandListCopy]
        const result = brandList.filter(brand => brand.name_e.toUpperCase().indexOf(this.keywords.toUpperCase()) > -1 ||
            brand.name_c.indexOf(this.keywords) > -1)

        this.brandList = result
        this.clickedIndex = 0
      },
    },
  }
</script>
<style  lang="scss" scoped>
.search {
	margin-bottom: 10px;
  display:flex;
}
.tab {
	border-bottom: 1px solid #ccc;
  .tab-letter{
    margin:0;
    padding:0;
    li{
      list-style: none;
    }
  }
}
.tab-item {
	padding: 8px 12px;
	border-radius: 4px 4px 0 0;
	border: 1px solid transparent;
	cursor: pointer;
}
.tab-item:hover {
	background: rgba(205, 174, 99, 0.3);
}
.tab-item.active {
	border: 1px solid #ccc;
	border-bottom: 1px solid #fff;
	position: relative;
	bottom: -1px;
}
.tab-content {
	height: 300px;
	overflow: auto;
	margin-top: 20px;
  .no-result {
    text-align: center;
    margin-top:20px;
  }
}
.brand-item {
	width: 25%;
	height: 40px;
	line-height: 40px;
	padding-right: 20px;
	display: inline-block;
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
	cursor: pointer;
	color: #337ab7;
}
.brand-item:hover {
	color: #666;
}
</style>
