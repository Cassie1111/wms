<template>
  <div class="page">
    <p class="page-title">营销中心 / 模版管理</p>

    <div class="nav">
      <div class="nav-name" v-if="accountType === 1 || banner.length">
        <router-link to="/banner-list">
          轮播配置
        </router-link>
      </div>
      <div class="nav-name show">
        <router-link :to="{name: 'activity-config'}">
          活动配置
        </router-link>
      </div>
      <div :class="accountType === 1 || banner.length ? 'w640' : 'w790'">
        <el-button type="primary" @click="addActivity" size="small" style="width: 140px"
                   v-if="accountType === 1 || activityConfig[0]['operator_types'].includes(1)">添加活动模版</el-button>
      </div>
    </div>

    <div class="page-content">
      <div class="activity-list" v-loading="configListLoading">
        <div class="activity-item" v-for="item in configList">
          <p class="activity-item-title">
            <span> {{item.title}} </span>
            <el-button type="text" @click="modify(item)"
                       v-if="accountType === 1 || activityConfig[0]['operator_types'].includes(1)">编辑</el-button>
          </p>

          <div class="activity-content">
            <div class="activity-goods" v-for="goods in item.goods_list">
              <div class="activity-goods-img">
                <img :src="goods.cover_image[0].path_full">
              </div>
              <p>{{goods.brand_name_e}}</p>
              <p>{{goods.currency_sign}}{{goods.official_price}} / {{goods.discount}}折</p>
              <p>秒杀价：HK$ {{goods.seckill_price}}</p>
            </div>

          </div>
        </div>
      </div>

      <div class="add-activity" v-if="addActivityFlag">
        <div class="activity-name">
          <label>模块名称</label>
          <el-input style="width: 60%" size="mini" v-model="saveData.title"></el-input>
        </div>
        <div v-if="showActivityList">
          <div class="activity-time">
            <label>显示时间</label>
            <el-date-picker type="datetime" placeholder="选择日期" style="width: 41%;" size="mini"
                            v-model="saveData.start_time" value-format="yyyy-MM-dd HH:mm:ss"
                            @change="timeChange"></el-date-picker>
            <span>-</span>
            <el-date-picker type="datetime" placeholder="选择日期" style="width: 41%;" size="mini"
                            v-model="saveData.end_time" value-format="yyyy-MM-dd HH:mm:ss"
                            @change="timeChange"></el-date-picker>
          </div>

          <p class="headline">选择活动</p>

          <el-table style="width: 100%" size="mini" :data="activityList.activity_list" header-align="center"
                    header-cell-class-name="table-thead" v-loading="activityListLoading">
            <el-table-column prop="title" label="活动名称" width="160" align="center" key="title"></el-table-column>
            <el-table-column prop="start_time" label="开始时间" width="90" align="center" key="time"></el-table-column>
            <el-table-column prop="goods_count" label="商品数量" width="75" align="center"
                             key="goodsCount"></el-table-column>
            <el-table-column prop="status_cn" label="活动状态" width="80" align="center" key="status"></el-table-column>
            <el-table-column label="操作" width="60" align="center" key="selectActivity">
              <template slot-scope="scope">
                <el-button type="text" size="mini" @click="selectActivityId(scope.row.id)"
                           v-if="saveData.activity_id != scope.row.id">选择
                </el-button>
                <el-button type="text" size="mini" v-else>已选择</el-button>
              </template>
            </el-table-column>
          </el-table>

          <el-pagination
              :small="true"
              v-if="activityList.total > activityListPage.count"
              @current-change="activityListChange"
              class="pagination"
              background
              layout="prev, pager, next, jumper"
              :page-size="activityListPage.count"
              :current-page="activityListPage.page"
              :total="activityList.total">
          </el-pagination>

          <el-button type="primary" @click="selectActivity" size="mini" style="width: 300px;margin: 20px 0 20px 80px;">
            下一步，选择模块商品
          </el-button>
        </div>

        <div v-if="!showActivityList">
          <div class="activity-time">
            <label>显示时间</label>
            <span>{{saveData.start_time}}</span>
            <span>-</span>
            <span>{{saveData.end_time}}</span>
          </div>

          <p class="headline">选择显示商品</p>

          <el-table style="width: 100%" size="mini" :data="activityDetail.goods_list" header-align="center"
                    header-cell-class-name="table-thead" v-loading="activityDetailLoading">
            <el-table-column prop="img" label="商品" width="205" align="left">
              <template slot-scope="scope">
                <div class="goods-info">
                  <div class="goods-img">
                    <img :src="scope.row.cover_image[0].path_full" alt="">
                  </div>
                  <p class="goods-title">
                    <el-tooltip effect="dark" placement="bottom-start">
                      <div slot="content"><pre>{{scope.row.goods_name}}</pre></div>
                      <span><pre>{{scope.row.goods_name}}</pre></span>
                    </el-tooltip>
                  </p>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="seckill_price" label="秒杀价(HK$)" width="100" align="center"></el-table-column>
            <el-table-column prop="stock" label="库存" width="100" align="center"></el-table-column>

            <el-table-column label="操作" width="60" align="center">
              <template slot-scope="scope">
                <el-checkbox @change="selectGood(scope.row.goods_no, $event)" v-model="scope.row.checked"></el-checkbox>
              </template>
            </el-table-column>
          </el-table>

          <el-pagination
              :small="true"
              v-if="activityDetail.goods_count > goodsListPage.count"
              @current-change="goodsListChange"
              class="pagination"
              background
              layout="prev, pager, next, jumper"
              :page-size="goodsListPage.count"
              :current-page="goodsListPage.page"
              :total="activityDetail.goods_count">
          </el-pagination>

          <div class="tac" style="margin-top: 20px;" v-if="backBtnShow">
            <el-button type="text" @click="back" size="mini">上一步</el-button>
          </div>
          <div class="tac">
            <el-button type="primary" size="mini" @click="save" style="width: 300px; margin-top: 20px">完成</el-button>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script>
  import { getConfigList, saveActivityConfig, getActivityConfigDetail } from '@/api/marketing/template'
  import { getActivityList, getActivityDetail } from '@/api/marketing/activity'
  import { mapGetters } from 'vuex'

  export default {
    name: 'activity-config',
    data() {
      return {
        // 上一步按钮是否展示
        backBtnShow: true,

        // 展示添加活动模版-选择活动
        addActivityFlag: false,

        // true：展示活动列表  false：展示商品列表
        showActivityList: true,
        configListLoading: true,
        activityListLoading: false,
        activityDetailLoading: true,
        configList: [],
        activityList: [],
        activityDetail: {},
        saveData: {
          config_id: 0,
          title: '',
          activity_id: '',
          start_time: '',
          end_time: '',
          goods_no_list: [],
        },
        activityListPage: {
          page: 1,
          count: 20,
        },
        goodsListPage: {
          page: 1,
          count: 20,
        },
      }
    },
    computed: {
      ...mapGetters([
        'accountType',
        'banner',
        'activityConfig',
      ]),
    },
    mounted() {
      this.getConfigList()
    },
    methods: {
      addActivity() {
        this.saveData = {
          config_id: 0,
          title: '',
          activity_id: '',
          start_time: '',
          end_time: '',
          goods_no_list: [],
        }
        this.activityList = []
        this.addActivityFlag = true
        this.showActivityList = true
        this.backBtnShow = true
      },
      getConfigList() {
        // 获取活动配置列表
        const that = this

        this.configListLoading = true

        getConfigList()
          .then(response => {
            that.configList = response.data.activity_list
            this.configListLoading = false
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      getActivityList(activityId) {
        // 获取活动列表
        const params = {
          start_time: this.saveData.start_time,
          end_time: this.saveData.end_time,
          page: this.activityListPage.page,
          count: this.activityListPage.count,
        }

        if (activityId) {
          params.activity_id = activityId
        }

        this.activityListLoading = true

        getActivityList(params)
          .then(response => {
            this.activityList = response.data
            this.activityListLoading = false
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      selectActivityId(id) {
        // 选择活动
        this.saveData.activity_id = id
      },
      selectActivity() {
        if (!this.saveData.activity_id) {
          this.$message({
            message: '请选择活动',
            type: 'warning',
          })

          return
        }

        this.showActivityList = false
        this.activityDetailLoading = true
        const params = {
          seckill_id: this.saveData.activity_id,
          page: this.goodsListPage.page,
          count: this.goodsListPage.count,
        }

        // 获取活动详情
        getActivityDetail(params)
          .then(response => {
            response.data.activity_detail.goods_list.forEach(item => {
              if (this.saveData.goods_no_list.indexOf(item.goods_no) > -1) {
                // eslint-disable-next-line
                item.checked = true
              } else {
                // eslint-disable-next-line
                item.checked = false
              }
            })

            this.activityDetail = response.data.activity_detail
            this.activityDetailLoading = false
          })
          .catch(err => {
            // 错误处理
            console.log(err)

            // 如果请求失败，重置活动详情商品列表数据和saveData中的商品列表
            this.activityDetail = {}
            this.saveData.goods_no_list = []
          })
          .finally(() => {
            // ...
          })
      },
      back() {
        this.showActivityList = true
        this.saveData.goods_no_list = []
      },
      selectGood(goodsNo, e) {
        if (e) {
          // 选中此商品
          this.saveData.goods_no_list.push(goodsNo)
        } else {
          const index = this.saveData.goods_no_list.indexOf(goodsNo)

          if (index > -1) {
            this.saveData.goods_no_list.splice(index, 1)
          }
        }
      },
      timeChange() {
        // 修改显示时间
        if (this.saveData.start_time && this.saveData.end_time) {
          const startTime = new Date(this.saveData.start_time).getTime()
          const endTime = new Date(this.saveData.end_time).getTime()

          if (startTime > endTime) {
            this.$message({
              message: '显示结束时间不能小于显示开始时间',
              type: 'warning',
            })

            return
          }

          this.getActivityList()
        }
      },
      modify(item) {
        const params = {
          config_id: item.id,
        }

        // 获取活动配置详情
        getActivityConfigDetail(params)
          .then(response => {
            const data = response.data.activity_detail

            this.saveData.title = data.title
            this.saveData.config_id = item.id
            this.saveData.activity_id = data.activity_id
            this.saveData.start_time = data.start_time
            this.saveData.end_time = data.end_time
            this.saveData.goods_no_list = data.goods_no_list
            this.addActivityFlag = true
            this.goodsListPage.page = 1
            this.backBtnShow = false
            this.selectActivity()
          })
          .catch(err => {
            // 错误处理
            console.log(err)
          })
          .finally(() => {
            // ...
          })
      },
      save() {
        if (this.saveData.goods_no_list.length !== 3) {
          this.$message({
            message: '请选择三个商品',
            type: 'error',
          })

          return
        }

        saveActivityConfig(this.saveData)
          .then(response => {
            this.$message({
              message: '添加成功',
              type: 'success',
            })

            setTimeout(() => {
              this.addActivityFlag = false
              this.getConfigList()
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
      activityListChange(val) {
        // 活动列表分页
        this.activityListPage.page = val
        this.getActivityList()
      },
      goodsListChange(val) {
        // 商品列表分页
        this.goodsListPage.page = val
        this.selectActivity()
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import "./index.scss";
</style>
