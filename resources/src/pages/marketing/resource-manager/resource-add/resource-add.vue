<template>
  <div class="page">
    <p class="page-title">营销中心 / 资源管理</p>

    <div class="page-content">
      <p class="nav">新建商品集</p>

      <div class="title">
        <label>标题</label>
        <el-input size="mini" v-model="saveData.title" style="width: 40%"></el-input>
      </div>
      <div class="cover-img">
        <label>封面图</label>
        <div class="upload-area" id="upload-area">
          <label id="upload-trigger" for="upload-files">上传照片</label>
          <input id="upload-files" type="file">
          <div class="img-area">
            <img v-if="coverImgPath" :src="coverImgPath"/>
            <img v-else src="../../../../assets/images/pages/marketing/upload-img.png"/>
          </div>
        </div>
      </div>

      <p class="nav">添加商品</p>

      <el-form :inline="true" :model="submitData" ref="form" label-position="left" label-width="60px" size="mini" class="form">
        <div>
          <el-form-item label="供应商:">
            <el-autocomplete
                size="mini"
                v-model="inputValue.supplier_name"
                value-key="supplier_name"
                :fetch-suggestions="supplierSearch"
                @select="supplierSelect"
            ></el-autocomplete>
          </el-form-item>
          <el-form-item label="品牌:">
            <el-autocomplete
                size="mini"
                v-model="inputValue.brand_name"
                value-key="name_e"
                :fetch-suggestions="brandSearch"
                @select="brandSelect"
            ></el-autocomplete>
          </el-form-item>
          <el-form-item label="品类:">
            <el-select v-model="submitData.type_id" placeholder="请选择">
              <el-option
                  v-for="item in typeList"
                  :key="item.id"
                  :label="item.label"
                  :value="item.value">
              </el-option>
            </el-select>
          </el-form-item>
        </div>
        <div>
          <el-form-item label="编码:">
            <el-input size="mini" v-model="submitData.code"></el-input>
          </el-form-item>
          <el-form-item label="名称:">
            <el-input size="mini" v-model="submitData.goods_name"></el-input>
          </el-form-item>
          <el-form-item style="margin-top: -2px">
            <el-button type="primary" @click="search">搜索</el-button>
          </el-form-item>
        </div>
      </el-form>

      <el-table style="width: 100%" size="mini" :data="goodsList.goods_list" header-align="center"
                header-cell-class-name="table-thead" row-class-name="table-tr" v-loading="loading">
        <el-table-column prop="img" label="货品" width="300" align="left">
          <template slot-scope="scope">
            <div class="goods-info">
              <div class="goods-img">
                <img :src="scope.row.cover_image[0].path_full" alt="">
              </div>
              <div class="goods-title">
                <el-tooltip effect="dark" placement="bottom-start">
                  <div slot="content"><pre>{{scope.row.goods_name}}</pre></div>
                  <span><pre>{{scope.row.goods_name}}</pre></span>
                </el-tooltip>
              </div>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="brand_name_e" label="品牌" width="100" align="center"></el-table-column>
        <el-table-column prop="product_price" label="供货价(HK$)" width="120" align="center"></el-table-column>
        <el-table-column prop="min_price" label="销售价(HK$)" width="120" align="center"></el-table-column>
        <el-table-column prop="stock" label="库存" width="80" align="center"></el-table-column>
        <el-table-column prop="supplier_name" label="供应商" width="150" align="center">
          <template slot-scope="scope">
            <span>{{scope.row.supplier_no}}</span><br>
            <span>{{scope.row.supplier_name}}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="80" align="center">
          <template slot-scope="scope">
            <span class="operate-btn" @click="addGoods(scope.row)" v-if="scope.row.is_add">移除</span>
            <span class="operate-btn" @click="addGoods(scope.row)" v-if="!scope.row.is_add">添加</span>
          </template>
        </el-table-column>
      </el-table>

      <div class="footer-operation">
        <el-checkbox @change="showAddGood($event)">仅显示已添加商品</el-checkbox>
        <el-pagination
            v-if="goodsList.total > submitData.count"
            @current-change="handleCurrentChange"
            class="pagination"
            background
            layout="prev, pager, next, jumper"
            :page-size="submitData.count"
            :current-page="submitData.page"
            :total="goodsList.total">
        </el-pagination>
      </div>



      <el-button size="mini" type="primary" style="margin: 0 0 20px 380px;width: 160px" @click="save">完成</el-button>
    </div>

  </div>
</template>

<script>
  import { getGoodsList, getSupplierList, getBrandList } from '@/api/marketing/marketing'
  import { saveResource, getResourceDetail } from '@/api/marketing/resource'
  import uploader from '@/utils/upload'

  export default {
    name: 'resource-add',
    data() {
      return {
        loading: true,
        inputValue: {
          supplier_name: '',
          brand_name: '',
        },
        supplierList: [],
        brandList: [],
        goodsList: [],
        typeList: [
          {
            label: '请选择',
            value: '',
          },
          {
            label: '箱包',
            value: '10001',
          },
          {
            label: '鞋履',
            value: '10002',
          },
          {
            label: '配饰',
            value: '10003',
          },
          {
            label: '服装',
            value: '10004',
          },
          {
            label: '首饰',
            value: '10005',
          },
          {
            label: '美容',
            value: '10006',
          },
          {
            label: '腕表',
            value: '10007',
          },
          {
            label: '生活用品',
            value: '10008',
          },
        ],
        addGoodsList: [],
        coverImgPath: '',
        submitData: {
          page: 1,
          count: 20,
          status: 1,
          supplier_no: '',
          brand_id: '',
          type_id: '',
          code: '',
          goods_name: '',
        },
        saveData: {
          collection_id: 0,
          title: '',
          cover_image: {
            path: '',
            width: '',
            height: '',
          },
          goods_no_list: [],
        },
      }
    },
    mounted() {
      if (this.$route.query.id) {
        this.saveData.seckill_id = this.$route.query.id
        this.getResourceDetail()
      } else {
        this.getGoodsList()
      }

      this.getSupplierList()
      this.getBrandList()

      // 初始化上传
      const uploadSetting = {
        browse_button: 'upload-trigger',
        container: 'upload-area',
        postfiles_button: 'upload-files',
        path_name: 'marketing/banner',
        max_file_size: '2M',
        filesAdded: () => {
          this.$message({
            showClose: true,
            message: '上传中',
            type: 'warning',
            onClose: () => {
              uploader.cancelUpload()
            },
          })
        },

        // 上传成功回调
        success: data => {
          this.$message.success('上传成功')
          this.coverImgPath = data.path_full
          this.saveData.cover_image.path = data.path
          this.saveData.cover_image.width = data.width
          this.saveData.cover_image.height = data.height
        },

        // 上传失败回调：包括前后端对上传的文件验证不通过、前后端网络异常
        error: msg => {
          this.$message.error(msg)
        },

        // 非必选项
        complete() {
          //
        },
      }

      uploader.init(uploadSetting)
    },
    methods: {
      getResourceDetail() {
        getResourceDetail(this.$route.query.id)
          .then(response => {
            const data = response.data.collection_detail

            this.saveData.collection_id = data.id
            this.saveData.title = data.title
            this.saveData.cover_image = data.cover_image
            this.coverImgPath = data.cover_image_path

            data.goods_list.forEach(item => {
              // eslint-disable-next-line
              item.is_add = true
            })

            this.addGoodsList = data.goods_list
            this.getGoodsList()
          })
          .catch(err => {
            //
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      getGoodsList() {
        // 获取商品列表
        this.loading = true
        getGoodsList(this.submitData)
          .then(response => {
            response.data.goods_list.forEach(item => {
              // eslint-disable-next-line
              item.is_add = false

              this.addGoodsList.forEach(goods => {
                if (goods.goods_no === item.goods_no) {
                  // eslint-disable-next-line
                  item.is_add = true
                }
              })
            })

            this.goodsList = response.data
            this.loading = false
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      addGoods(item) {
        if (item.is_add) {
          // 移除
          // eslint-disable-next-line
          item.is_add = false
          this.addGoodsList.forEach((goods, index) => {
            if (goods.goods_no === item.goods_no) {
              this.addGoodsList.splice(index, 1)
            }
          })
        } else {
          // 添加
          // eslint-disable-next-line
          item.is_add = true
          this.addGoodsList.push(item)
        }
      },
      handleCurrentChange(val) {
        // 分页操作
        this.submitData.page = val
        this.getGoodsList()
      },
      getSupplierList() {
        getSupplierList()
          .then(response => {
            this.supplierList = response.data.supplier_list
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      getBrandList() {
        getBrandList()
          .then(response => {
            this.brandList = response.data.brand_list
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      supplierSearch(queryString, cb) {
        const restaurants = this.supplierList
        const results = queryString ? restaurants.filter(this.createFilter(queryString)) : restaurants

        // 调用 callback 返回建议列表的数据
        cb(results)

        const itemInfo = {
          supplier_no: '',
        }

        if (results.length && queryString !== '') {
          results.forEach(item => {
            if (item.supplier_name === queryString) {
              itemInfo.supplier_no = item.supplier_no
            }
          })
          this.supplierSelect(itemInfo)
        }

        if (results.length === 0 || queryString === '') {
          this.supplierSelect(itemInfo)
        }
      },
      brandSearch(queryString, cb) {
        const restaurants = this.brandList
        const results = queryString ? restaurants.filter(this.createFilter(queryString)) : restaurants

        // 调用 callback 返回建议列表的数据
        cb(results)

        const itemInfo = {
          brand_id: '',
        }

        if (results.length && queryString !== '') {
          results.forEach(item => {
            if (item.name_e === queryString) {
              itemInfo.brand_id = item.brand_id
            }
          })
          this.brandSelect(itemInfo)
        }

        if (results.length === 0 || queryString === '') {
          this.brandSelect(itemInfo)
        }
      },
      createFilter(queryString) {
        return restaurant => restaurant.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0
      },
      supplierSelect(item) {
        this.submitData.supplier_no = item.supplier_no
      },
      brandSelect(item) {
        this.submitData.brand_id = item.brand_id
      },
      search() {
        this.getGoodsList()
      },
      showAddGood(e) {
        // 显示已添加商品
        if (e) {
          // 将total设置为0 是为了不展示分页
          this.goodsList.total = 0
          this.goodsList.goods_list = this.addGoodsList
        } else {
          this.submitData.page = 1
          this.getGoodsList()
        }
      },
      save() {
        // 防止商品多次添加
        this.saveData.goods_no_list = []

        this.addGoodsList.forEach(item => {
          this.saveData.goods_no_list.push(item.goods_no)
        })

        saveResource(this.saveData)
          .then(response => {
            this.$message({
              message: '保存成功',
              type: 'success',
            })

            setTimeout(() => {
              this.$router.push({
                name: 'resource-list',
              })
            }, 2000)
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
    },
  }
</script>


<style lang="scss" scoped>
  .page-title,
  .page-content {
    width: 100%;
    background-color: #ffffff;
  }

  .page-title {
    margin-bottom: 10px;
    line-height: 40px;
    padding-left: 20px;
  }

  .nav {
    padding: 25px 0 10px 40px;
  }

  .page-content .title,
  .cover-img {
    display: flex;
    padding-left: 40px;
    padding-top: 20px;
    label {
      width: 60px;
      padding-right: 10px;
    }
  }

  .upload-area {
    position: relative;
    label {
      position: absolute!important;
      opacity: 0;
      width: 300px;
      height: 150px;
    }
    input {
      display: none;
    }
    .img-area {
      img {
        width: 300px;
        height: 150px;
      }
    }
  }

  .form {
    padding: 20px 0 20px 40px;
    div {
      display: flex;
      justify-content: flex-start;
    }
  }

  .operate-btn {
    color: #cdae63;
    cursor: pointer;
  }

  .table-tr td {
    padding: 20px 10px;
  }

  .goods-info {
    display: flex;
    padding: 30px 0px;
    .goods-title {
      width: 190px;
      line-height: 80px;
      margin-left: 10px;
      vertical-align:middle;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .goods-img {
      width: 80px;
      height: 80px;
      img {
        width: 100%;
        height: 100%;
      }
    }
  }

  .footer-operation {
    display: flex;
    justify-content: space-between;
    margin: 20px 10px;
    .pagination {
      text-align: right;
    }
  }
</style>
