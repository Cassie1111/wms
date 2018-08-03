<template>
  <div class="page message-content">
    <div class="content-title">公告详情</div>
    <div class="content-info" v-loading="loading">
      <div class="info-decription">
        <p class="decription-title">{{announcementObj.title}}</p>
        <p class="decription-time">{{announcementObj.create_time}}</p>
      </div>
      <div class="info-detitle" v-html="announcementObj.content"></div>
    </div>
  </div>
</template>

<script>
  import { getNoticeDetail } from '@/api/message/message'

  export default {
    data() {
      return {
        announcementObj: {},
        loading: true,
      }
    },

    created() {
      this.init()
    },

    methods: {
      init() {
        getNoticeDetail(this.$route.params.id)
          .then(data => {
            this.loading = false
            this.announcementObj = data.data
          })
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
      padding: 50px 20px 0;
      background-color: $background-color;
      .info-decription {
        text-align: center;
        border-bottom: 1px solid #cacaca;
        .decription-title {
          height: 12px;
          line-height: 12px;
          margin-bottom: 24px;
          font-size: 16px;
          color: #333;
        }
        .decription-time {
          height: 14px;
          line-height: 14px;
          margin-bottom: 16px;
          font-size: 14px;
          color: #666;
        }
      }
      .info-detitle {
        padding: 30px 10px;
        line-height: 26px;
        font-size: 14px;
        color: #666;
      }
    }
  }
</style>