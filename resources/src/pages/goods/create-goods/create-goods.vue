<template>
  <div class="page">
    <!--面包屑导航-->
    <el-breadcrumb separator="/" class="location">
      <el-breadcrumb-item>货品管理</el-breadcrumb-item>
      <el-breadcrumb-item>创建新商品</el-breadcrumb-item>
    </el-breadcrumb>

    <div class="main-content page-content search-page-content">
      <!--搜索类目 -->
      <div class="flex_align_center">
        <label>搜索类目</label>
        <el-autocomplete ref="querySearch"
                         class="inline-input fetch-search"
                         :fetch-suggestions="querySearch"
                         placeholder="请输入关键字或者首字母"
                         :trigger-on-focus="false"
                         v-model="fetchSearch"
                         @select="handleSelect"
        ></el-autocomplete>
        <el-button type="primary" @click="quickSearch" size="mini" icon="el-icon-search" class="btn-query">查询</el-button>
      </div>

      <!-- 类目list -->
      <div class="goods-category flex_x_between">
        <!-- 一二级类目 -->
        <div class="category-list" v-loading="isLoading">
          <div class="list-search">
            <el-autocomplete
                :fetch-suggestions="localSearch"
                placeholder="请输入关键字或者首字母"
                :trigger-on-focus="false"
                @select="handleLocalSelect"
                v-model="localQuery1"
            >
              <i class="el-icon-search el-input__icon"
                 slot="suffix"></i>
            </el-autocomplete>
          </div>
          <div class="list-main" ref="category_list_1">
            <ul class="category_level_1">
              <li v-for="item in type_tree" :key="item.id">
                {{item.type_name}}
                <ul class="category_level_2">
                  <li v-for="sub_item in item.child_type_info"
                      :key="sub_item.id"
                      :class="sub_item.type_name == selectedType[1] ? 'active' : '' "
                      @click="getSubTree(item,sub_item)"
                      :ref="sub_item.id"
                  >
                    {{sub_item.type_name}}
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
        <!-- 三级类目-->
        <div class="category-list">
          <div class="list-search">
            <el-autocomplete
                :fetch-suggestions="localSearch2"
                placeholder="请输入关键字或者首字母"
                :trigger-on-focus="false"
                @select="handleLocalSelect"
                v-model="localQuery2"
                >
              <i class="el-icon-search el-input__icon"
                 slot="suffix"></i>
            </el-autocomplete>
          </div>
          <div class="list-main" ref="category_list_2">
            <ul class="category_level_1">
              <li v-for="item in type_tree_2" :key="item.id">
                {{item.first_letter}}
                <ul class="category_level_2">
                  <li v-for="sub_item in item.child_type_info"
                      :key="sub_item.id"
                      :class="sub_item.type_name == selectedType[2] ? 'active' : '' "
                      @click="getSubTree(item,sub_item)"
                      :ref="sub_item.id"
                  >
                    {{sub_item.type_name}}
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>

        <!-- 四级类目-->
        <div class="category-list">
          <div class="list-search">
            <el-autocomplete
                :fetch-suggestions="localSearch3"
                placeholder="请输入关键字或者首字母"
                :trigger-on-focus="false"
                @select="handleLocalSelect"
                v-model="localQuery3"
            >
              <i class="el-icon-search el-input__icon"
                 slot="suffix"></i>
            </el-autocomplete>
          </div>
          <div class="list-main" ref="category_list_3">
            <ul class="category_level_1">
              <li v-for="item in type_tree_3" :key="item.id">
                {{item.first_letter}}
                <ul class="category_level_2">
                  <li v-for="sub_item in item.child_type_info"
                      :key="sub_item.id"
                      :class="sub_item.type_name == selectedType[3] ? 'active' : '' "
                      @click="getSubTree(item,sub_item)"
                      :ref="sub_item.id"
                  >
                    {{sub_item.type_name}}
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>

      </div>
      <div style="padding:10px;font-size:12px;border-bottom: 1px solid #ddd;">
        您当前选择的类目:{{selectedCate}}
      </div>

      <div class="flex_x_center btn-create-area">
        <el-button type="primary" size="mini" :disabled="!canCreate" @click="createGoods">创建商品</el-button>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
  @import "create-goods.scss";
</style>

<script>
  import { searchProductQuoteType, getLastestAnnouncement, readRemindInfo } from '@/api/goods/createGoods'
  import { getCates } from '@/api/goods/sellingGoods'

  export default {
    name: 'create-goods',
    data() {
      return {
        canCreate: false,
        cateId: 0,
        cateName: '',
        firstCateId: 0,
        firstCateName: '',
        virtualCateId: 0,
        isShowDetail: false,
        isLoading: true,
        announcement: '',
        announceId: '',
        selectedType: [],
        type_tree: [],
        type_tree_2: [],
        type_tree_3: [],
        fetchSearch: '',
        localQuery1: '',
        localQuery2: '',
        localQuery3: '',
      }
    },
    created() {
      this._getCates()
    },
    mounted() {
      this.changeType = this.$route.params.changeType
    },
    computed: {
      selectedCate() {
        return this.selectedType.join(' > ')
      },
    },
    methods: {
      createGoods() {
        this.$router.push({
          path: '/goods/create-goods/create',
          query: {
            cate_id: this.cateId,
            cate_name: this.cateName,
            virtual_cate_id: this.virtualCateId,
            first_cate_id: this.firstCateId,
            first_cate_name: this.firstCateName,
            cate_value: encodeURIComponent(this.selectedType.join(',')),
            changeType: this.changeType,
          },
        })
      },
      _getCates() {
        getCates().then(res => {
          this.type_tree = res.data.type_tree
        }).finally(() => {
          this.isLoading = false
        })
      },

      // 本地搜索（三个类目list内部的搜索，顶部的搜索是向服务器发出请求处理的）
      localSearch(queryString, cb) {
        const res = []

        this.type_tree.forEach(item => {
          item.child_type_info.forEach(subItem => {
            if (
              queryString[0].toUpperCase() === subItem.first_letter ||
              subItem.type_name.indexOf(queryString) > -1
            ) {
              res.push({
                value: subItem.type_name,
                id: subItem.id,
                sup_value: item.type_name,
                level: subItem.level,
              })
            }
          })
        })
        cb(res)
      },
      localSearch2(queryString, cb) {
        const res = []

        this.type_tree_2.forEach(item => {
          item.child_type_info.forEach(subItem => {
            if (
              queryString[0].toUpperCase() === subItem.first_letter ||
              subItem.type_name.indexOf(queryString) > -1
            ) {
              res.push({
                value: subItem.type_name,
                id: subItem.id,
                sup_value: item.first_letter,
                level: subItem.level,
              })
            }
          })
        })
        cb(res)
      },
      localSearch3(queryString, cb) {
        const res = []

        this.type_tree_3.forEach(item => {
          item.child_type_info.forEach(subItem => {
            if (
              queryString[0].toUpperCase() === subItem.first_letter ||
              subItem.type_name.indexOf(queryString) > -1
            ) {
              res.push({
                value: subItem.type_name,
                id: subItem.id,
                sup_value: item.first_letter,
                level: subItem.level,
              })
            }
          })
        })
        cb(res)
      },

      // 本地搜索
      handleLocalSelect(e) {
        const subVal = e.value
        const supVal = e.sup_value
        const level = Number(e.level)

        this.$refs[e.id][0].click()
        this.scrollView(
          this.$refs[e.id][0],
          this.$refs[`category_list_${level - 1}`]
        )
        if (level === 2) {
          this.$set(this.selectedType, '0', supVal)
          this.$set(this.selectedType, '1', subVal)
        }
        if (level > 2) {
          this.$set(this.selectedType, level - 1, subVal)
        }
      },

      // 获取子类目树
      getSubTree(sup, sub) {
        const level = Number(sub.level)

        if (level === 2) {
          this.selectedType = []
          this.type_tree_2 = []
          this.type_tree_3 = []
          this.$set(this.selectedType, '0', sup.type_name)
          this.$set(this.selectedType, '1', sub.type_name)
          this.firstCateId = sup.cate_id
          this.firstCateName = sup.type_name
        }
        if (level === 3) {
          this.$set(this.selectedType, '2', sub.type_name)
          if (this.selectedType.length === 4) {
            this.selectedType.pop()
          }
        }
        if (level === 4) {
          this.$set(this.selectedType, '3', sub.type_name)
        }
        if (sub.child_type_info.length === 0) {
          this.cateId = sub.cate_id
          this.virtualCateId = sub.id
          this.cateName = sub.type_name
          this.canCreate = true
        } else {
          this.canCreate = false
        }
        const attr = `type_tree_${sub.level}`

        this[attr] = sub.child_type_info
      },
      querySearch(queryString, cb) {
        this.queryFilter(queryString, cb)
      },
      queryFilter(queryString, cb) {
        const params = {
          keywords: queryString,
        }

        searchProductQuoteType(params).then(res => {
          const queryRes = res.data.type_list || []
          let cbArr = []

          queryRes.forEach(item => {
            const result = this.formatCateInfo(item)
            const id = []
            let value = ''

            // eslint-disable-next-line
            result.forEach(item => {
              value += `${item.value} > `
              id.push(item.id)
            })
            cbArr.push({
              value: value.substring(0, value.length - 2),
              id,
            })
          })
          const noResult = {
            value: '没有匹配项',
            state: 0,
          }

          cbArr = cbArr.length === 0 ? [noResult] : cbArr
          cb(cbArr)
        })
      },
      handleSelect(e) {
        const cates = e.value.split(' > ')
        const ids = e.id

        if (Number(e.state) === 0) {
          this.fetchSearch = ''

          return
        }

        this.fetchSearch = cates[cates.length - 1]
        this.selectedType = cates
        this.queryString = e.state === 0 ? '' : cates[cates.length - 1]
        this.$refs[ids[1]][0].click()
        this.scrollView(this.$refs[ids[1]][0], this.$refs['category_list_1'])
        this.$nextTick(() => {
          this.$refs[ids[2]][0].click()
          this.scrollView(this.$refs[ids[2]][0], this.$refs['category_list_2'])
          if (ids.length === 4) {
            this.$nextTick(() => {
              this.$refs[ids[3]][0].click()
              this.scrollView(
                this.$refs[ids[3]][0],
                this.$refs['category_list_3']
              )
            })
          }
        })
      },

      // 滚动选中类目到视图可见范围
      scrollView(inner, outer) {
        const deltaY = inner.parentNode.parentNode.offsetTop - outer.offsetTop

        // eslint-disable-next-line
        outer.scrollTop = deltaY
      },
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
      showDetail() {
        this.isShowDetail = !this.isShowDetail
      },

      // 点击搜索按钮
      quickSearch() {
        const params = {
          keywords: this.fetchSearch,
        }

        if (this.fetchSearch.length !== 0) {
          searchProductQuoteType(params).then(res => {
            const queryRes = res.data.type_list || []

            let cbArr = []

            queryRes.forEach(item => {
              const result = this.formatCateInfo(item)
              const id = []
              let value = ''

              result.forEach(subItem => {
                value += `${subItem.value} > `
                id.push(subItem.id)
              })
              cbArr.push({
                value: value.substring(0, value.length - 2),
                id,
              })
            })
            const noResult = {
              value: '没有匹配项',
              state: 0,
            }

            cbArr = cbArr.length === 0 ? [noResult] : cbArr
            this.$refs.querySearch.activated = true
            this.$refs.querySearch.suggestions = cbArr
          })
        }
      },
    },
  }
</script>
