<template>
  <div class="page">
    <p class="title">营销中心 / 活动管理 / 秒杀活动</p>

    <div class="page-content" v-if="!confirmFlag">
      <p class="page-title">创建新活动（秒杀）</p>

      <el-form label-width="110px" label-position="left" :model="saveData" :rules="rules" ref="saveData">
        <div class="input-box">
          <el-form-item prop="title" label="活动名称">
            <el-input size="mini" v-model="saveData.title"></el-input>
          </el-form-item>
          <el-form-item prop="start_time" label="活动开始时间">
            <el-date-picker type="datetime" placeholder="选择日期" size="mini"
                            v-model="saveData.start_time" value-format="yyyy-MM-dd HH:mm:ss"
                            :disabled="startTimeDisabled"></el-date-picker>
          </el-form-item>
        </div>

        <div class="input-box">
          <el-form-item prop="end_time" label="活动结束时间">
            <el-date-picker type="datetime" placeholder="选择日期" size="mini" v-model="saveData.end_time"
                            value-format="yyyy-MM-dd HH:mm:ss"></el-date-picker>
          </el-form-item>
          <el-form-item prop="preheating_time" label="活动预热时间">
            <el-date-picker type="datetime" placeholder="选择日期" size="mini"
                            v-model="saveData.preheating_time" value-format="yyyy-MM-dd HH:mm:ss"></el-date-picker>
          </el-form-item>
        </div>

        <p class="page-title">添加活动商品：</p>

        <el-form :inline="true" :model="submitData" ref="form" label-position="left" label-width="80px" size="mini" class="form">
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
            <el-form-item label="品牌货号:">
              <el-input size="mini" v-model="submitData.product_model"></el-input>
            </el-form-item>
            <el-form-item label="编码:">
              <el-input size="mini" v-model="submitData.code"></el-input>
            </el-form-item>
            <el-form-item label="名称:">
              <el-input size="mini" v-model="submitData.goods_name"></el-input>
            </el-form-item>
            <el-form-item style="margin-top: -2px">
              <el-button type="primary" @click="search" size="mini">搜索</el-button>
            </el-form-item>
          </div>
        </el-form>

        <p class="search-num">共搜索到<span>{{goodsList.total}}</span>个商品</p>

        <el-table style="width: 100%" size="mini" :data="goodsList.goods_list" header-align="center"
                  header-cell-class-name="table-thead" v-loading="loading">
          <el-table-column label="货品" width="350" align="left">
            <template slot-scope="scope">
              <div class="goods-info">
                <div class="goods-img">
                  <img :src="scope.row.cover_image[0].path_full" alt="">
                </div>
                <div class="goods-title">
                  <p>{{scope.row.brand_name_e}}</p>
                  <el-tooltip effect="dark" placement="bottom-start">
                    <div slot="content"><pre>{{scope.row.goods_name}}</pre></div>
                    <p><pre>{{scope.row.goods_name}}</pre></p>
                  </el-tooltip>
                </div>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="product_price" label="供货价(HK$)" width="120" align="center"></el-table-column>
          <el-table-column prop="min_price" label="销售价(HK$)" width="120" align="center"></el-table-column>
          <el-table-column prop="stock" label="库存" width="120" align="center"></el-table-column>
          <el-table-column label="供应商" width="160" align="center">
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

        <div class="page-title">
          <el-checkbox @change="showAddGood($event)" v-model="showAddGoodsFlag">仅显示已添加商品</el-checkbox>
        </div>

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

        <el-button type="primary" size="mini" @click="submitForm('saveData')"
                   style="margin: 20px 0 50px 400px;">下一步，设置商品活动价
        </el-button>
      </el-form>
    </div>

    <div class="page-content" v-if="confirmFlag">
      <div class="activity-info">
        <p>活动名称：{{saveData.title}} <span class="operate-btn" @click="showPage">修改</span></p>
        <p>活动时间：{{saveData.start_time}} 至 {{saveData.end_time}}</p>
        <p>预热时间：{{saveData.preheating_time}}</p>
        <p>优惠方式：秒杀</p>
      </div>

      <p class="page-title">设置秒杀价</p>
      <el-table style="width: 100%" size="mini" :data="addGoodsList" header-align="center"
                header-cell-class-name="table-thead" v-loading="setPriceLoading">
        <el-table-column label="货品" width="350" align="left">
          <template slot-scope="scope">
            <div class="goods-info">
              <div class="goods-img">
                <img :src="scope.row.cover_image[0].path_full" alt="">
              </div>
              <div class="goods-title">
                <p>{{scope.row.brand_name_e}}</p>
                <el-tooltip effect="dark" placement="bottom-start">
                  <div slot="content"><pre>{{scope.row.goods_name}}</pre></div>
                  <p><pre>{{scope.row.goods_name}}</pre></p>
                </el-tooltip>
              </div>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="product_price" label="供货价(HK$)" width="120" align="center"></el-table-column>
        <el-table-column prop="format_min_price" label="销售价(HK$)" width="120" align="center"></el-table-column>
        <el-table-column prop="stock" label="库存" width="120" align="center"></el-table-column>
        <el-table-column label="秒杀价(HK$)" width="160" align="center">
          <template slot-scope="scope">
            <el-input type="number" min="0" size="mini" v-model="scope.row.seckill_price"></el-input>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="80" align="center">
          <template slot-scope="scope">
            <span class="operate-btn" @click="addGoods(scope.row)">移除</span>
          </template>
        </el-table-column>
      </el-table>
      <el-button type="primary" size="mini" style="margin: 20px 0 50px 430px;" @click="save">确认创建</el-button>
    </div>
  </div>
</template>

<script>
  import { getGoodsList, getSupplierList, getBrandList } from '@/api/marketing/marketing'
  import { saveActivity, getActivityDetail, getSeckillPrice } from '@/api/marketing/activity'

  export default {
    name: 'activity-add',
    data() {
      // 活动时间校验
      const endTime = (rule, value, callback) => {
        if (!value) {
          callback(new Error('请填写活动结束时间'))
        } else {
          const startTime = new Date(this.saveData.start_time).getTime()
          const endTime = new Date(value).getTime()

          if (startTime > endTime) {
            callback(new Error('活动结束时间不能小于活动开始时间'))
          } else {
            callback()
          }
        }
      }

      const preheatingTime = (rule, value, callback) => {
        if (!value) {
          callback(new Error('请填写活动预热时间'))
        } else {
          const startTime = new Date(this.saveData.start_time).getTime()
          const preheatingTime = new Date(value).getTime()

          if (startTime < preheatingTime) {
            callback(new Error('活动预热时间不能大于活动开始时间'))
          } else {
            callback()
          }
        }
      }

      return {
        loading: true,
        setPriceLoading: true,
        confirmFlag: false,
        showAddGoodsFlag: false,
        startTimeDisabled: false,
        inputValue: {
          supplier_name: '',
          brand_name: '',
        },
        supplierList: [],
        brandList: [],
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
        goodsList: [],
        addGoodsList: [],
        saveData: {
          seckill_id: 0,
          title: '',
          start_time: '',
          end_time: '',
          preheating_time: '',
          goods_list: [],
        },
        submitData: {
          page: 1,
          count: 20,
          status: 1,
          supplier_no: '',
          brand_id: '',
          type_id: '',
          product_model: '',
          code: '',
          goods_name: '',
        },
        rules: {
          title: [
            {
              required: true,
              message: '请填写标题',
              trigger: 'blur',
            },
          ],
          start_time: [
            {
              required: true,
              message: '请填写活动开始时间',
              trigger: 'change',
            },
          ],
          end_time: [
            {
              required: true,
              validator: endTime,
              trigger: 'change',
            },
          ],
          preheating_time: [
            {
              required: true,
              validator: preheatingTime,
              trigger: 'change',
            },
          ],
        },
      }
    },
    mounted() {
      if (this.$route.query.id) {
        this.saveData.seckill_id = this.$route.query.id
        this.getActivityDetail()
      } else {
        this.getGoodsList()
      }

      this.getSupplierList()
      this.getBrandList()
    },
    methods: {
      getActivityDetail() {
        const params = {
          seckill_id: this.$route.query.id,
        }

        getActivityDetail(params)
          .then(response => {
            const data = response.data.activity_detail

            if (data.status === 2) {
              // 活动已开始不可修改活动开始时间
              this.startTimeDisabled = true
            }

            this.saveData.title = data.title
            this.saveData.start_time = data.start_time
            this.saveData.end_time = data.end_time
            this.saveData.preheating_time = data.preheating_time

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
            // 判断商品是否已经被添加
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
      addGoods(item) {
        const that = this

        if (item.is_add) {
          // 移除
          // eslint-disable-next-line
          item.is_add = false
          that.addGoodsList.forEach((goods, index) => {
            if (goods.goods_no === item.goods_no) {
              that.addGoodsList.splice(index, 1)
            }
          })
        } else {
          // 添加
          // eslint-disable-next-line
          item.is_add = true
          that.addGoodsList.push(item)
        }
      },
      submitForm(formName) {
        this.$refs[formName].validate(valid => {
          if (valid) {
            this.setPrice()

            return true
          }
          console.log('error submit!!')

          return false
        })
      },
      setPrice() {
        // 设置商品活动价格
        if (!this.addGoodsList.length) {
          this.$message({
            message: '请添加商品',
            type: 'error',
          })

          return
        }

        this.confirmFlag = !this.confirmFlag
        const goodsNo = []

        this.addGoodsList.forEach(item => {
          goodsNo.push(item.goods_no)
        })

        const params = {
          goods_no_list: goodsNo,
          start_time: this.saveData.start_time,
          end_time: this.saveData.end_time,
        }

        this.setPriceLoading = true

        // 获取带有秒杀价格的商品列表
        getSeckillPrice(params)
          .then(response => {
            response.data.goods_list.forEach(item => {
              // eslint-disable-next-line
              item.is_add = true
            })

            this.addGoodsList = response.data.goods_list
            this.setPriceLoading = false
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      showPage() {
        // 修改已添加商品（上一步）
        this.confirmFlag = !this.confirmFlag
      },
      save() {
        // foreach 方法不支持break 用try catch抛出异常终止
        try {
          // 防止商品多次被添加
          this.saveData.goods_list = []

          this.addGoodsList.forEach(item => {
            if (!item.seckill_price) {
              this.$message({
                message: '请填写秒杀价',
                type: 'error',
              })

              throw new Error()
            }

            // eslint-disable-next-line
            if (parseInt(item.seckill_price) > parseInt(item.min_price)) {
              this.$message({
                message: '秒杀金额不能高于销售价格',
                type: 'error',
              })

              throw new Error()
            }

            const info = {
              goods_no: item.goods_no,
              seckill_price: Math.ceil(item.seckill_price),
            }

            this.saveData.goods_list.push(info)
          })
        } catch (e) {
          return false
        }

        if (this.saveData.goods_list.length) {
          // 只有设置了秒杀价才能保存
          saveActivity(this.saveData)
            .then(response => {
              this.$message({
                message: '保存成功',
                type: 'success',
              })

              setTimeout(() => {
                this.$router.push({
                  name: 'activity-list',
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

          return true
        }

        return false
      },
      handleCurrentChange(val) {
        // 分页操作
        this.submitData.page = val
        this.getGoodsList()
      },
      showAddGood(e) {
        // 显示已添加商品
        this.showAddGoodsFlag = e
        if (e) {
          // 将total设置为0 是为了不展示分页
          this.goodsList.total = 0
          this.goodsList.goods_list = this.addGoodsList
        } else {
          this.submitData.page = 1
          this.getGoodsList()
        }
      },
    },
  }
</script>


<style scoped lang="scss">
  .title,
  .page-content {
    width: 100%;
    background-color: #ffffff;
  }

  .title {
    line-height: 40px;
    margin-bottom: 10px;
    padding-left: 20px;
  }

  .input-box {
    display: flex;
    padding-left: 20px;
    & > div {
      width: 320px;
      margin: 20px 40px 0 0;
    }
    .el-input {
      width: 200px;
    }
  }

  .page-title {
    padding: 30px 0 10px 20px;
  }

  .operate-btn {
    cursor: pointer;
    color: #CDAE63;
  }

  .form {
    padding: 20px 0 0px 40px;
    div {
      display: flex;
      justify-content: flex-start;
    }
  }

  .table-thead th {
    padding: 8px 0 !important;
  }

  .search-num {
    padding: 0 0 10px 20px;
    span {
      padding: 0 5px;
      color: #cdae63;
    }
  }

  .activity-info {
    padding: 30px 0 0 20px;
    p {
      line-height: 30px;
    }
    span {
      padding-left: 20px;
    }
  }

  .goods-info {
    display: flex;
    padding: 30px 0px;
    .goods-title {
      margin-top: 20px;
      margin-left: 10px;
      p {
        width: 240px;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
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

  .pagination {
    text-align: center;
    margin: 20px 0;
  }

</style>
