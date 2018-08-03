<template>
  <div class="page">
    <p class="page-title">营销中心 / 活动管理 / 秒杀活动</p>

    <div class="page-content">
      <div class="nav">
        <div>活动列表</div>
        <el-button size="mini" type="primary" @click="linkToAdd"
                   v-if="accountType === 1 || activityManager[0]['operator_types'].includes(1)">创建新秒杀</el-button>
      </div>
      <el-table :data="activityList.activity_list" style="width: 100%;padding: 0 20px;" v-loading="loading">
        <el-table-column prop="title" label="活动名称" width="260" align="center"></el-table-column>
        <el-table-column label="活动时间" width="260" align="center">
          <template slot-scope="scope">
            <span>{{scope.row.start_time}}</span><br>
            <span>至</span><br>
            <span>{{scope.row.end_time}}</span><br>
          </template>
        </el-table-column>
        <el-table-column prop="goods_count" label="活动商品数" width="90" align="center"></el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="180" align="center"></el-table-column>
        <el-table-column label="操作" width="120" align="center">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="editActivity(scope.row.id)"
                       v-if="accountType === 1 || activityManager[0]['operator_types'].includes(1)">编辑</el-button>
            <br>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
          v-if="activityList.total > pageData.count"
          @current-change="handleCurrentChange"
          class="pagination"
          background
          layout="prev, pager, next, jumper"
          :page-size="pageData.count"
          :current-page="pageData.page"
          :total="activityList.total">
      </el-pagination>
    </div>
  </div>
</template>

<script>
  import { getActivityList } from '@/api/marketing/activity'
  import { mapGetters } from 'vuex'

  export default {
    name: 'activity-list',
    data() {
      return {
        loading: true,
        activityList: [],
        pageData: {
          page: 1,
          count: 20,
        },
      }
    },
    computed: {
      ...mapGetters(['accountType', 'activityManager']),
    },
    mounted() {
      this.getActivityList()
    },
    methods: {
      getActivityList() {
        this.loading = true

        // 获取活动列表
        getActivityList(this.pageData)
          .then(response => {
            this.activityList = response.data
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
      handleCurrentChange(val) {
        // 分页操作
        this.pageData.page = val
        this.getActivityList()
      },
      editActivity(id) {
        this.$router.push({
          name: 'activity-add',
          query: {
            id,
          },
        })
      },
      linkToAdd() {
        this.$router.push({
          name: 'activity-add',
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
    line-height: 40px;
    padding-left: 20px;
  }

  .page-content {
    margin-top: 10px;
    height: 940px;
    overflow: auto;
  }

  .nav {
    display: flex;
    padding: 20px;
    justify-content: space-between;
  }

  .pagination{
    text-align: center;
    margin: 20px 0;
  }

</style>