<template>
  <div class="header">
    <div class="header-content">
      <div class="content-logo">
      </div>
      <div class="content-option">
        <a class="option-container option-message" href="/message/entry" target="_blank"
          :class="{'hover' : messageShow}"
          v-on:mouseenter="showMessage"
          v-on:mouseleave="showMessage">
          <span class="option-background background--message"></span>消息
          <em class="option-count message-count" v-if="path !== '/message/entry'">
            ({{messageObj.unreadt_total ? messageObj.unreadt_total : 0}})
          </em>
          <span class="option-triangel" v-show="messageObj.message_list && messageObj.message_list.length>0"></span>
          <ul class="message-list" target="_blank" v-show="messageObj.message_list && messageObj.message_list.length>0">
            <span class="list-tips"></span>
            <li class="list-item" v-for="(item, index) in messageObj.message_list" v-if="index < 5">
              <router-link to="/message/entry" target="_blank" class="message-read">
                <span v-if="item.message_type == 1">【交易】</span>
                <span v-if="item.message_type == 2">【系统】</span>
                {{item.title}}
              </router-link>
            </li>
          </ul>
        </a>
        <div class="option-container"
          :class="{'hover' : logoutShow}"
          v-on:mouseenter="showLogout"
          v-on:mouseleave="showLogout">
          <span class="option-background background--supplier"></span>
          <span class="message-name">{{userInfo.username ? userInfo.username : '--'}}</span>
          <span class="option-triangel"></span>
          <ul class="message-list logout">
            <span class="list-tips"></span>
            <li class="list-item" @click="toLogout">
              <em>退出登录</em>
            </li>
          </ul>
        </div>
        <router-link class="option-container" to="/" @click.native="flushPage">
          <span class="option-background background--home"></span>返回后台首页
        </router-link>
        <router-link class="option-container" target="_blank" to="/service" v-if="accountType === 1 || service.length > 0">
          <span class="option-background background--service"></span>客服中心
          <em v-show="!isService" class="option-count">
            ({{serviceobj.conversation_list && serviceobj.conversation_list.total_unread_cnt
            ? serviceobj.conversation_list.total_unread_cnt : '0'}})
          </em>
        </router-link>
      </div>
    </div>
    <div class="header-message" v-if="announcementObj.notice_list && announcementObj.notice_list.length > 0">
      {{announcementObj.notice_list[0].title}}
      <router-link :to="'/announcement/detial/' + announcementObj.notice_list[0].id " target="_blank" class="message-read">立即查看</router-link>
    </div>
    <el-dialog
      title="提示"
      :visible.sync="dialogVisible"
      width="30%"
      :modal-append-to-body="false">
      <span>确认退出？</span>
      <span slot="footer" class="dialog-footer">
                <el-button size="mini" @click="dialogVisible = false">取 消</el-button>
                <el-button size="mini" type="primary" @click="logout">确 定</el-button>
            </span>
    </el-dialog>
  </div>
</template>

<script>
  import { mapGetters, mapState } from 'vuex'
  import { getMessageList } from '@/api/message/message'
  import { getServiceInfo } from '@/api/service/service'
  import { logout } from '@/api/auth/login'

  const MESSAGE = 0
  const ANNOUNCEMENT = 1
  const LOOP_INTERVAL_TIME = 5000

  export default {
    data() {
      return {
        announcementObj: {},
        dialogVisible: false,
        isService: false,
        logoutShow: false,
        loopNewMsgStatus: true,
        messageShow: false,
        messageObj: {},

        paramsData: {
          page: 1,
        },
        path: '',
        serviceobj: {},
      }
    },

    computed: {
      ...mapState({
        userInfo: state => state.user.userInfo,
      }),
      ...mapGetters(['accountType', 'service']),
    },

    watch: {
      $route() {
        this.path = this.$route.path
      },
    },

    created() {
      this.init()
      this.getMessage()
    },

    beforeDestroy() {
      clearTimeout(this.loopNewMsgTimer)
    },

    methods: {
      flushPage() {
        this.$emit('leftMenuChange')
        this.$router.push({
          path: '/',
          query:{
            t: `${new Date().getTime()}${Math.floor(Math.random()*9999+1000)}`
          }
        })
      },

      handleClose(done) {
        this.$confirm('确认关闭？')
          .then(_ => {
            done()
          })
      },

      // 初始化
      init() {
        if (this.$route.path !== '/service') {
          // 请求客服接口
          getServiceInfo()
            .then(data => {
              this.serviceobj = data.data
            })
            .finally(() => {
              this.loopNewMsgStatus = false

              this.loopMessage()
            })
        } else {
          this.isService = true
        }
      },

      // 请求消息公告
      getMessage() {
        // 请求消息接口
        getMessageList(this.paramsData.page, MESSAGE)
          .then(data => {
            this.messageObj = data.data
          })

        // 请求公告接口
        getMessageList(this.paramsData.page, ANNOUNCEMENT)
          .then(data => {
            this.announcementObj = data.data

            if (data.data.notice_list && data.data.notice_list.length > 0) {
              this.$nextTick(() => {
                this.$emit('serviceWindowResize')
              })
            }
          })
      },

      // 退出登录操作
      logout() {
        this.dialogVisible = false

        logout().then(() => {
          this.$router.push('/login')
          localStorage.removeItem('token')
        })
      },

      // 消息轮询
      loopMessage() {
        if (this.loopNewMsgStatus) return
        this.loopNewMsgStatus = true
        this.loopNewMsgTimer = setTimeout(() => {
          this.init()
        }, LOOP_INTERVAL_TIME)
      },

      // 显示退登浮层
      toLogout() {
        this.dialogVisible = true
      },

      // 更新消息列表展示状态
      showMessage() {
        this.messageShow = !this.messageShow
      },

      showLogout() {
        this.logoutShow = !this.logoutShow
      },
    },
  }
</script>

<style lang="scss" scoped>
  @mixin flex {
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
  }
  .header {
    /*position: -webkit-sticky;*/
    /*position: sticky;*/
    /*top: 0;*/
    width: 100%;
    margin: 0 auto;
    margin-bottom: 10px;
    // z-index: 1999;
    .header-content {
      @include flex();
      -webkit-box-pack: space-between;
      -ms-flex-pack: space-between;
      justify-content: space-between;
      height: 63px;
      align-items: center;
      padding: 0 120px;
      background-color: #cdae63;
      .content-logo {
        width: 163px;
        height: 30px;
        background: url('../../assets/images/common/main-header/logo.png');
        background-size: contain;
        background-repeat: no-repeat;
      }
      .content-option {
        @include flex();
        font-size: 14px;
        color: #fff;
        .option-container {
          position: relative;
          @include flex();
          height: 60px;
          padding-left: 24px;
          align-items: center;
          color: #fff;
          text-decoration: none;
          cursor: pointer;
          .background--message {
            background: url('../../assets/images/common/main-header/message.png');
          }
          .background--supplier {
            background: url('../../assets/images/common/main-header/supplier.png');
          }
          .background--home {
            background: url('../../assets/images/common/main-header/home.png');
          }
          .background--service {
            background: url('../../assets/images/common/main-header/service.png');
          }
          .option-background {
            width: 16px;
            height: 14px;
            margin-right: 8px;
            margin-top: -2px;
            background-size: contain;
            background-repeat: no-repeat;
          }
          .option-count {
            margin-left: 8px;
            font-style: normal;
            color: red;
          }
          .option-triangel {
            width: 10px;
            height: 6px;
            margin-left: 8px;
            background: url('../../assets/images/common/main-header/triangle.png');
            background-size: 100%;
            background-repeat: no-repeat;
          }
          .message-list {
            position: absolute;
            top: 50px;
            right: 9px;
            @include flex();
            flex-direction: column;
            width: 300px;
            height: 0;
            margin: 0;
            padding-left: 0;
            box-shadow: 1px 2px 3px 0px rgba(100,100,100,0.5);
            border-radius: 2px;
            background-color: #fff;
            z-index: 3;
            .list-item {
              height: 42px;
              line-height: 42px;
              padding: 0 20px;
              overflow: hidden;
              text-overflow: ellipsis;
              white-space: nowrap;
              font-style: normal;
              list-style: none;
              color: #666;
              a {
                display: inline-block;
                width: 100%;
                font-style: normal;
                cursor: pointer;
              }
            }
            .list-item a:hover {
              color: #cdae63;
              text-decoration: none;
            }
            .list-item em:hover {
              color: #cdae63;
            }
            .list-tips {
              position: absolute;
              top: -6px;
              right: 32px;
              width: 10px;
              height: 0;
              background: url('../../assets/images/common/main-header/triangle.png');
              background-size: 100%;
              background-repeat: no-repeat;
              transform:rotate(180deg);
              -ms-transform:rotate(180deg);
              -moz-transform:rotate(180deg);
              -webkit-transform:rotate(180deg);
              -o-transform:rotate(180deg);
            }
          }
          .message-name {
            display: inline-block;
            max-width: 260px;
            overflow: hidden;
            text-overflow:ellipsis;
            white-space: nowrap;
          }
          .logout {
            width: 109px;
            li {
              padding: 0;
              margin: auto;
              border: none;
              em {
                font-style: normal;
                cursor: pointer;
              }
            }
          }
          .container-welcome {
            margin-right: 20px;
          }
          .container-login {
            color: #fff;
          }
        }
        .option-container:after {
          content: '';
          width: 1px;
          height: 13px;
          margin-left: 24px;
          background-color: #dcdcdc;
        }
        .option-container:last-child:after {
          width: 0;
          margin-left: 0;
        }
        .hover {
          .message-list {
            height: auto;
            .list-item {
              border-bottom: 1px solid #cacaca;
            }
            .list-item:last-child {
              border-bottom: none;
            }
            .list-tips {
              height: 6px;
            }
          }
        }
      }
    }
    .header-message {
      height: 40px;
      margin: auto;
      text-align: center;
      line-height: 40px;
      font-size: 14px;
      color: #666;
      background-color: #f4eedf;
      cursor: pointer;
      .message-read {
        color: #4998CF;
      }
      .message-read:active {
        color: #cdae63;
      }
    }
  }
</style>
