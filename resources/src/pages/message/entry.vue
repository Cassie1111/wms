<template>
  <div class="page message-content">
    <div class="content-title">消息</div>
    <div class="content-info">
      <div class="info-tips">
        <span :class="{'active' : slectedIndex == index}" v-for="(item, index) in tipsList" @click="changeTips(index)">
          {{item}}
        </span>
      </div>
      <div class="info-list" v-loading="loading">
        <messageList v-show="slectedIndex == 0" :list="messageList" :requierFlag="requierFlagMsg" type="message"></messageList>
        <messageList v-show="slectedIndex == 1" :list="announcementList" :requierFlag="requierFlagAnt" type="announcement"></messageList>
      </div>
    </div>
    <el-pagination
      background
      @current-change="handleCurrentChange"
      :current-page.sync="paramsData.page"
      :page-size="20"
      layout="total, prev, pager, next, jumper"
      :total="total"
      v-if="containerList.length > 0 && total > 20">
    </el-pagination>
  </div>
</template>
<script>
  import { getMessageList, updateMessage } from '@/api/message/message'
  import messageList from './message-list.vue'

  const READSTATUS = 1

  export default {
    components: {
      messageList,
    },

    data() {
      return {
        // 公告list
        announcementList: [],

        // 公告总数
        announcementTotal: 1,

        // 公告页数
        announcementPage: 1,

        // 列表容器
        containerList: [],

        loading: true,

        // 消息list
        messageList: [],

        // 消息总数
        messageTotal: 1,

        // 消息页数
        messagePage: 1,

        tipsList: ['我的消息', '公告'],

        // 翻页总数
        total: 1,

        paramsData: {
          page: 1,
        },

        // 是否请求过服务
        requierFlagMsg: false,

        requierFlagAnt: false,

        // 当前选中标签
        slectedIndex: 0,
      }
    },

    created() {
      this.init()
    },

    methods: {
      // 切换选中标签，并还原之前请求页码
      changeTips(index) {
        if (this.slectedIndex === index) return
        this.slectedIndex = index
        if (index === 0) {
          this.containerList = this.messageList
          this.total = this.messageTotal
          this.paramsData.page = this.messagePage
        } else {
          this.containerList = this.announcementList
          this.total = this.announcementTotal
          this.paramsData.page = this.announcementPage
        }

        if (!this.requierFlagAnt) {
          this.init()
        }
      },

      // 初始化
      init() {
        this.loading = true
        getMessageList(this.paramsData.page, this.slectedIndex)
          .then(data => {
            this.loading = false
            this.total = data.data.total
            window.scrollTo(0, 0)

            if (this.slectedIndex === 0) {
              this.messageList = data.data.message_list
              this.containerList = data.data.message_list
              this.messageTotal = data.data.total
              this.messagePage = this.paramsData.page
              this.requierFlagMsg = true

              // 修改消息读取状态
              if (this.messagePage === 1) {
                updateMessage()
              }
            } else {
              this.announcementList = data.data.notice_list
              this.containerList = data.data.notice_list
              this.announcementTotal = data.data.total
              this.announcementPage = this.paramsData.page
              this.requierFlagAnt = true
            }
          })
      },

      handleCurrentChange() {
        this.init()
      },
    },
  }
</script>

<style lang="scss">
  .message-content {
    .content-info {
      .el-loading-mask {
        .el-loading-spinner {
          top: 200px;
        }
      }
    }
    .el-pagination {
      margin-bottom: 40px;
    }
  }
</style>

<style lang="scss" scoped>
  $background-color: #fff;
  $font-color: #666;
  $active-color: #cdae63;
  $fonts-normal: 14px;
  .message-content {
    font-size: $fonts-normal;
    color: $font-color;
    background-color: $background-color;
    .content-title {
      height: 50px;
      line-height: 40px;
      padding-left: 20px;
      font-size: $fonts-normal;
      font-weight: normal;
      color: #333;
    }
    .content-title:after {
      content: "";
      display: block;
      height: 10px;
      margin-left: -20px;
      background-color: #f6f6f6;
    }
    .content-info {
      min-height: 630px;
      padding-top: 30px;
      background-color: $background-color;
      .info-tips {
        height: 40px;
        margin-bottom: 30px;
        border-bottom: 2px solid $active-color;
        span {
          display: inline-block;
          width: 150px;
          height: 40px;
          line-height: 40px;
          text-align: center;
          cursor: pointer;
        }
        span:hover {
          color: #cdae63;
        }
        .active {
          color: #cdae63;
          border: 2px solid $active-color;
          border-bottom: 2px solid $background-color;
        }
      }
    }
  }
  .el-pagination {
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
  }
</style>