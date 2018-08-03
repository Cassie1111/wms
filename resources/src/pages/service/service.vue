<template>
  <div class="service-page">
    <main-header @serviceWindowResize="recalculateHeight"></main-header>
    <div class="service-page-body flex">
      <div class="chat-list">
        <div class="owner flex">
          <div class="owner-avatar" :style="{backgroundImage: 'url(' + ownerAvatar + ')'}"></div>
          <span class="owner-nickname" :title="ownerNickname">{{ ownerNickname }}</span>
        </div>
        <!--搜索区域-->
        <div class="search flex">
          <el-input
            placeholder="搜索"
            prefix-icon="el-icon-search"
            v-model.trim="search"
            @focus="focusSearch"
            @blur="blurSearch"
            @keydown.enter.stop.native="enterSearch"
          >
          </el-input>
        </div>
        <div class="tabs">
          <!--供、销 列表切换tab-->
          <div class="tabs-header">
            <div class="tabs-nav">
              <div
                class="tabs-item"
                :class="{ active: activeTab === FIELD_RETAILER_USER }"
                @click="tabSwitchListener(FIELD_RETAILER_USER)">
                <span>销</span>
                <template v-if="inactiveTabNewMsgCount > 0 && activeTab === FIELD_SUPPLIER_USER">
                  <el-badge :value="inactiveTabNewMsgCount" :max="99" class="item red-hot"></el-badge>
                </template>
              </div>
              <div
                class="tabs-item"
                :class="{ active: activeTab === FIELD_SUPPLIER_USER }"
                @click="tabSwitchListener(FIELD_SUPPLIER_USER)">
                <span>供</span>
                <template v-if="inactiveTabNewMsgCount > 0 && activeTab === FIELD_RETAILER_USER">
                  <el-badge :value="inactiveTabNewMsgCount" :max="99" class="item red-hot"></el-badge>
                </template>
              </div>
            </div>
          </div>
          <!--左侧用户列表-->
          <div class="tabs-content">
            <div class="tab-pane" ref="userList">
              <template v-if="userListReady">
                <div class="unread">
                  <div
                    v-for="(item, index) in unreadList"
                    class="user flex"
                    :class="{ active: activeUid === item.uid }"
                    @click="handleUserSelected(item.uid, item.name, item.avatar, index, UNREAD)"
                    :key="index + activeTab"
                  >
                    <div class="avatar" :style="{backgroundImage: 'url(' + item.avatar + ')'}"></div>
                    <div class="user-item">
                      <div class="user-item-nickname">{{ item.name }}</div>
                      <div class="user-item-msg" v-if="item.newest_msg.msg_type === TYPE_MSG_TXT_STR">
                        {{ item.newest_msg.msg_body.content }}
                      </div>
                      <div class="user-item-msg" v-else>{{ DEFAULT_IMG_TIPS }}</div>
                    </div>
                    <el-badge :value="item.unread_cnt" :max="99" class="item red-hot"></el-badge>
                  </div>
                </div>
                <div class="read">
                  <div
                    v-for="(item, index) in readList"
                    class="user flex"
                    :class="{ active: activeUid === item.uid }"
                    @click="handleUserSelected(item.uid, item.name, item.avatar, index, READ)"
                    :key="index + activeTab"
                  >
                    <div class="avatar" :style="{backgroundImage: 'url(' + item.avatar + ')'}"></div>
                    <div class="user-item">
                      <div class="user-item-nickname">{{ item.name }}</div>
                      <div class="user-item-msg" v-if="item.newest_msg.msg_type === TYPE_MSG_TXT_STR">
                        {{ item.newest_msg.msg_body.content }}
                      </div>
                      <div class="user-item-msg" v-else>{{ DEFAULT_IMG_TIPS }}</div>
                    </div>
                    <template v-if="activeUserNewMsgCount > 0 && activeUid === item.uid">
                      <el-badge :value="activeUserNewMsgCount" :max="99" class="item red-hot"></el-badge>
                    </template>
                  </div>
                </div>
              </template>
            </div>
          </div>
          <!--搜索结果列表-->
          <div
            class="tabs-content search-result"
            v-show="searchFocus === true"
            @mouseover="overSearchList"
            @mouseout="outSearchList"
          >
            <div
              class="tab-pane"
              ref="searchList"
              @click="clickSearchList"
            >
              <div class="read">
                <div
                  class="user flex"
                  v-for="(item, index) in searchList"
                  @click="handleSearchSelected(item.uid, item.name, item.avatar, item.user_type, $event)"
                  :key="index"
                >
                  <div class="avatar" :style="{backgroundImage: 'url(' + item.avatar + ')'}"></div>
                  <div class="user-item">
                    <div class="user-item-nickname">{{ item.name }}</div>
                    <div class="user-item-msg">
                      {{ item.msg }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--右侧消息记录、聊天框区域-->
      <div class="chat-container">
        <div class="dialogue-box">
          <div class="dialogue-head flex">{{ selectedNickname }}</div>
          <div class="dialogue-body" ref="dialogueBody">
            <!--消息列表区域-->
            <div class="dialog-box" ref="dialogBox">
              <transition name="fade">
                <div class="no-more" v-if="noMore">别拉了，真的没有了...</div>
              </transition>
              <template v-for="(item, index) in messageList">
                <!--消息发送/接收时间-->
                <div class="chat-time">{{ item.msg_time}}</div>
                <!--接收方消息-->
                <div v-if="item.from_id === activeUid" class="chat-others chat-content flex">
                  <img class="avatar" :src="selectedAvatar" />
                  <p v-if="item.msg_type === TYPE_MSG_TXT_STR" class="msg flex">
                    {{ item.msg_body.content}}
                    <img class="icon" src="../../assets/images/pages/service/arrow-left.png">
                  </p>
                  <p v-else-if="item.msg_type === TYPE_MSG_IMG_STR" class="msg flex">
                    <img
                      :src="item.msg_body.url"
                      :style="{width: item.msg_body.width + 'px', height: item.msg_body.height + 'px'}"
                      @click="emitPreview(item.imgUuid)"
                    />
                    <img class="icon" src="../../assets/images/pages/service/arrow-left.png">
                  </p>
                </div>
                <!--发送方消息-->
                <div v-else class="chat-self chat-content flex">
                  <div class="chat-status">
                    <i v-if="item.msg_push_status === 'warning'" class="el-icon-warning"></i>
                    <i v-else-if="item.msg_push_status === 'loading'" class="el-icon-loading"></i>
                  </div>
                  <p v-if="item.msg_type === TYPE_MSG_TXT_STR" class="msg flex">
                    {{ item.msg_body.content}}
                    <img class="icon" src="../../assets/images/pages/service/arrow-right.png">
                  </p>
                  <p v-else-if="item.msg_type === TYPE_MSG_IMG_STR" class="msg flex">
                    <img
                      :src="item.msg_body.url"
                      :style="{width: item.msg_body.width + 'px', height: item.msg_body.height + 'px'}"
                      @click="emitPreview(item.imgUuid)"
                    />
                    <img class="icon" src="../../assets/images/pages/service/arrow-right.png">
                  </p>
                  <img class="avatar" :src="ownerAvatar" />
                </div>
              </template>
            </div>
            <!--发送消息区域 textarea-->c
            <div class="sendbox" ref="sendbox">
              <div class="sendbox-bar">
                <!--上传图片-->
                <div @click="sendImgIntercept($event)" class="icon-upload upload-area" id="upload-area" title="发送图片">
                  <label id="upload-trigger" for="upload-files"></label>
                  <input id="upload-files" type="file">
                </div>
              </div>
              <div class="sendbox-area">
                <textarea
                  v-model="message"
                  ref="textarea"
                  @keyup.enter="sendTxtListener"
                >
                </textarea>
                <div class="sendbox-btn">
                  <el-button type="primary" @click="sendTxtListener">发送</el-button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--图片预览-->
    <div>
      <picturePreview
        ref='picturePreview'
        :pictureList="imgList"
        :pictureListIsShow="false"
      ></picturePreview>
    </div>
  </div>
</template>

<script>
  /*eslint max-lines: "off"*/
  import debounce from 'debounce'
  import MainHeader from '@/components/main-header/main-header'
  import uploader from '@/utils/upload'
  import picturePreview from '@/components/pages/picture-preview/picture-preview'
  import { timeFormat } from '@/utils/time'
  import * as api from '@/api/service/service'
  
  // TODO 模块拆分
  // 保留body中原有的样式
  const oldStyle = []
  const win = window
  const doc = document
  const body = doc.body

  // 重写body样式，不出现垂直滚动条
  function rewriteGlobalStyle() {
    const style = body.style

    oldStyle.bodyOverflowY = style.overflowY
    style.overflowY = 'hidden'
  }

  // 恢复body原有样式
  function retrieveGlobalStyle() {
    body.style.overflowY = oldStyle.bodyOverflowY
  }

  // 获取到页面顶部距离
  function getTopPosition(srcElement) {
    let ele = srcElement
    let top = 0

    while (ele) {
      top += ele.offsetTop
      ele = ele.offsetParent
    }

    return top
  }

  // uuid
  function generateUuid() {
    return new Date().getTime().toString(36) + Number(String(Math.random()).substring(2, 18)).toString(36)
  }

  const PAGE_COUNT = 30
  const CALL_TYPE_FIRST_REQUEST = 1
  const CALL_TYPE_NEW_MSG = 2
  const CALL_TYPE_HISTORY_MSG = 3
  const TYPE_MSG_TXT = 1
  const TYPE_MSG_TXT_STR = 'txt'
  const TYPE_MSG_IMG = 2
  const TYPE_MSG_IMG_STR = 'img'
  const DEFAULT_IMG_TIPS = '[图片]'
  const LOOP_INTERVAL_TIME = 3000
  const IMG_MAX_WIDTH = 272
  const IMG_DOMAIN = 'https://com-ofashion-supply-chain.oss-cn-beijing.aliyuncs.com'
  const FIELD_RETAILER_USER = 'retailer_user'
  const FIELD_SUPPLIER_USER = 'supplier_user'
  const FIELD_RETAILER_INT = 1
  const FIELD_SUPPLIER_INT = 2
  const UNREAD = 'unread'
  const READ = 'read'
  const CONFLICT_TIPS = '已经有其他小伙伴在服务这位客户啦～'

  function noop() {}

  // TODO 数据、图片格式处理
  function handleConversationList(list) {
    return list.map(item => {
      item.avatar = `${IMG_DOMAIN + item.avatar}?x-oss-process=image/resize,w_320`

      return item
    })
  }

  const service = {
    name: 'service',

    // customer_service 官方客服环信等配置信息
    // activeTab 当前选中的"销"、"供"选项，二选一：retailer_user、supplier_user
    // activeUid 左侧用户列表被选中的用户uid
    // contactId 用户加锁id，每次与用户建立连接时需要重新生成唯一值
    // contactAvailable 当前用户会话是否可用，即未被其他用户占用
    // unreadConversationList 左侧用户列表---"未读消息"， 用户列表按消息状态分为两部分：未读、已读
    // readConversationList 左侧用户列表---"已读消息"
    // messageList 右侧的消息列表---新消息、历史消息
    // message 用户输入框textarea值
    // activeUserNewMsgCount 被选中用户的"未读"消息数，当右侧消息列表区滚动到底部时重置"未读"消息数为0
    // inactiveTabNewMsgCount 未被选中tab的"未读"消息数
    data() {
      // TODO 将一些const数据全部放入data里
      return {
        ownerAvatar: IMG_DOMAIN + this.$store.state.user.userInfo.avatar,
        ownerNickname: this.$store.state.user.userInfo.username,
        customer_service: {},

        activeTab: FIELD_RETAILER_USER,
        activeUid: '',
        FIELD_RETAILER_USER,
        FIELD_SUPPLIER_USER,
        FIELD_RETAILER_INT,
        FIELD_SUPPLIER_INT,
        UNREAD,
        READ,
        contactId: '',
        contactAvailable: false,
        unreadConversationList: {},
        readConversationList: {},

        userListReady: false,
        selectedNickname: '请选择用户',
        selectedAvatar: '',
        TYPE_MSG_TXT,
        TYPE_MSG_IMG,
        TYPE_MSG_TXT_STR,
        TYPE_MSG_IMG_STR,
        DEFAULT_IMG_TIPS,
        messageList: [],
        message: '',
        noMore: false,
        activeUserNewMsgCount: 0,
        inactiveTabNewMsgCount: 0,
        imgList: [],

        search: '',
        searchFocus: false,
        searchStart: 0,
        searchList: [],
      }
    },

    created() {
      //
    },

    mounted() {
      // 获取未读、已读消息用户列表，客服环信信息
      Promise.all([
        this.getConfig(),
        this.unread(),
        this.read(),
      ])
        .then(() => {
          this.userListReady = true

          // 轮询获取"未读消息"用户列表
          this.loopUnread()
        })
        .catch(err => {
          this.$message.warning(err)
        })

      // 重写body样式
      rewriteGlobalStyle()

      // 计算聊天框高度
      this.recalculateHeight()

      // 绑定window窗口resize、聊天框scroll事件、搜索结果列表scroll事件
      this.handleWindowResizeEvent()
      this.handleDialogScrollEvent()
      this.handleSearchScrollEvent()

      // 绑定图片上传事件
      this.handleUploadEvent()

      this.handleWindowCloseEvent()
    },

    beforeDestroy() {
      retrieveGlobalStyle()
      this.handleWindowResizeEvent(false)
      clearTimeout(this.loopNewMsgTimer)
      clearTimeout(this.loopUnreadTimer)

      // 释放当前会话状态
      this.releaseMsgContact()
    },

    methods: {
      // 获取左侧的"未读消息"用户列表
      unread(callback = noop) {
        return api.unreadList()
          .then(response => {
            const conversationList = response.data.conversation_list

            conversationList[this.FIELD_RETAILER_USER] =
              handleConversationList(conversationList[this.FIELD_RETAILER_USER])
            conversationList[this.FIELD_SUPPLIER_USER] =
              handleConversationList(conversationList[this.FIELD_SUPPLIER_USER])

            // "未读"用户列表----去重逻辑
            ;[this.FIELD_RETAILER_USER, this.FIELD_SUPPLIER_USER].forEach(key => {
              conversationList[key].forEach((user, index) => {
                // 如果是被选中的用户，则此时应该是"已读"状态---所以需要从"未读"用户列表中删除，防止出现两个状态
                if (user.uid === this.activeUid) {
                  conversationList[key].splice(index, 1)
                }
              })

              conversationList[key].forEach((user, index) => {
                // 从"已读"用户列表中删除获取到的"未读"用户
                if (this.readConversationList[key]) {
                  this.readConversationList[key].forEach((subuser, subindex) => {
                    if (user.uid === subuser.uid) {
                      this.readConversationList[key].splice(subindex, 1)
                    }
                  })
                }
              })
            })

            // 判断这次获取到的unread"未读"列表是否与上次的一致，否则需要更新read"已读"列表
            const currReLen = conversationList[this.FIELD_RETAILER_USER].length
            const currSupLen = conversationList[this.FIELD_SUPPLIER_USER].length

            if (this.unreadRetailerUserCount !== currReLen || this.unreadSupplierUserCount !== currSupLen) {
              if (this.unreadRetailerUserCount !== undefined) {
                // this.read()
              }

              this.unreadRetailerUserCount = currReLen
              this.unreadSupplierUserCount = currSupLen
            }

            // 未被选中的tab栏--"未读"消息数
            let retailerUnreadCount = 0
            let supplierUnreadCount = 0

            ;[this.FIELD_RETAILER_USER, this.FIELD_SUPPLIER_USER].forEach(key => {
              conversationList[key].forEach((user, index) => {
                if (this.FIELD_RETAILER_USER === key) {
                  retailerUnreadCount += user.unread_cnt
                } else {
                  supplierUnreadCount += user.unread_cnt
                }
              })
            })

            this.inactiveTabNewMsgCount = this.activeTab === this.FIELD_RETAILER_USER
              ? supplierUnreadCount
              : retailerUnreadCount

            this.unreadConversationList = conversationList
          })
          .catch(err => {
            this.$message.warning(err)
          })
          .finally(() => {
            callback()
          })
      },

      // 获取左侧的"已读消息"用户列表
      read() {
        return api.readList()
          .then(response => {
            const conversationList = response.data.conversation_list

            conversationList[this.FIELD_RETAILER_USER] =
              handleConversationList(conversationList[this.FIELD_RETAILER_USER])
            conversationList[this.FIELD_SUPPLIER_USER] =
              handleConversationList(conversationList[this.FIELD_SUPPLIER_USER])

            this.readConversationList = response.data.conversation_list
          })
          .catch(err => {
            this.$message.warning(err)
          })
      },

      // 获取环信客服配置信息
      getConfig() {
        return api.getConfig()
          .then(response => {
            this.customer_service = response.data.customer_service
          })
          .catch(err => {
            this.$message.warning(err)
          })
      },

      // 选中左侧列表的用户项 TODO 参数优化
      handleUserSelected(selectedUid, selectedNickname, selectedAvatar, index, readStatus) {
        if (selectedUid === this.activeUid) return

        this.clearMsgLoop()
        this.releaseMsgContact()
        this.resetMsgState()

        this.activeUid = selectedUid
        this.selectedNickname = selectedNickname
        this.selectedAvatar = selectedAvatar

        this.contactId = `${this.activeUid + new Date().getTime().toString(36) +
        Number(String(Math.random()).substring(2, 18)).toString(36)}0000000000000000`.substring(0, 21)

        const params = {
          recv_uid: this.activeUid,
          contact_id: this.contactId,
          call_type: CALL_TYPE_FIRST_REQUEST,
        }

        clearTimeout(this.loopNewMsgTimer)

        this.pullMsg(params, () => {
          this.scrollToBottom()

          // 定时循环方式---获取最新消息
          this.loopNewMsg()
        })

        // 未读消息--"红点"处理，此时用户选中了"未读"用户
        if (readStatus !== this.UNREAD) return

        // const readList = this.readConversationList[this.activeTab]

        this.unreadConversationList[this.activeTab].forEach((item, unreadIndex) => {
          // 将"未读"用户从"未读"列表中移出，并将其加入"已读"列表
          if (unreadIndex === index) {
            this.readConversationList[this.activeTab].unshift(this.unreadConversationList[this.activeTab][unreadIndex])
            this.unreadConversationList[this.activeTab].splice(unreadIndex, 1)
          }
        })

        // this.read()
      },

      // 选中搜索结果--左侧列表的用户项 TODO 参数优化
      handleSearchSelected(selectedUid, selectedNickname, selectedAvatar, userType, event) {
        this.activeTab = userType

        // 重置search相关状态、操作
        this.resetSearchState()

        let index = 0
        let readStatus = ''

        this.readConversationList[this.activeTab].forEach((item, readIndex) => {
          if (selectedUid === item.uid) {
            index = readIndex
            readStatus = this.READ
          }
        })

        this.unreadConversationList[this.activeTab].forEach((item, unreadIndex) => {
          if (selectedUid === item.uid) {
            index = unreadIndex
            readStatus = this.UNREAD
          }
        })

        this.handleUserSelected(selectedUid, selectedNickname, selectedAvatar, index, readStatus)

        this.$nextTick(() => {
          event.target.scrollIntoView()
        })
      },

      // 重置一些消息记录相关属性
      resetMsgState() {
        this.noMore = false
        this.messageList = []
        this.imgList = []
        this.newId = ''
        this.historyId = ''
        this.scrollLoading = false
        this.contactAvailable = true
        this.activeUserNewMsgCount = 0
      },

      // 清除右侧的聊天区域"最新消息"轮询
      clearMsgLoop() {
        clearInterval(this.msgLoopTimer)
      },

      // 释放会话
      releaseMsgContact() {
        if (!this.activeUid || !this.contactId || !this.contactAvailable) return

        const params = {
          recv_uid: this.activeUid,
          contact_id: this.contactId,
        }

        api.releaseConversation(params)
          .then(response => {
            //
          })
          .catch(err => {
            this.$message.warning(err)
          })
      },

      // 拉取最新、历史消息
      pullMsg(params, finallyFunc = noop) {
        // 获取历史消息时，必须有一个消息id
        if (CALL_TYPE_HISTORY_MSG === params.call_type) {
          if (this.noMore || !this.historyId) return
        }

        // 轮询时遇到的case处理：没有最新的id，在切换左侧列表中用户时导致该问题
        if (CALL_TYPE_NEW_MSG === params.call_type && (!this.newId || !params.id)) return

        // 在两个用户之间快速来回选中时遇到的case：contact_id不一致，在releaseMsgContact响应慢时会导致该问题
        if (CALL_TYPE_FIRST_REQUEST === params.call_type && this.contactId !== params.contact_id) {
          clearTimeout(this.loopNewMsgTimer)

          return
        }

        api.pullMsg(params)
          .then(response => {
            // 防止网络慢导致的数据延迟，判断响应数据里的uid是否与当前的用户uid一致
            if (this.activeUid !== params.recv_uid) return

            const data = response.data
            const messageList = data.conversation_list

            if (data.contact_available === false) {
              this.$message.warning(data.contact_remind_msg || CONFLICT_TIPS)
              this.contactAvailable = data.contact_available

              return
            }

            // 非最新消息（增量请求）
            if (CALL_TYPE_NEW_MSG !== params.call_type) {
              // 记录最原始的消息id
              this.historyId = messageList.length !== 0 && messageList[0].id
            }

            // 历史消息
            if (CALL_TYPE_HISTORY_MSG === params.call_type) {
              if (messageList.length < PAGE_COUNT) this.noMore = true
            }

            // 记录最新消息id
            if (CALL_TYPE_HISTORY_MSG !== params.call_type && messageList.length > 0) {
              this.newId = messageList[messageList.length - 1].id
            }

            // 增量更新----去重逻辑
            // 发送消息时（pushMsg、sendImg）自己模拟创建了消息数据格式（消息能快速在页面上展示），
            // 导致在增量更新时会有重复消息数据
            // hack手段：接口生成的每条记录都是有id字段
            messageList.forEach((item, index) => {
              /*eslint no-param-reassign: "off"*/
              // 展示图片时，样式是固定宽度
              if (item.msg_body.url !== undefined) {
                let width = Number.parseInt(item.msg_body.width, 10)
                let height = Number.parseInt(item.msg_body.height, 10)

                if (width > IMG_MAX_WIDTH) {
                  width = IMG_MAX_WIDTH
                  height = (IMG_MAX_WIDTH / item.msg_body.width) * item.msg_body.height
                }

                item.msg_body.width = width
                item.msg_body.height = height
              }

              // 使用轮询到的最新消息替换自己模拟发送的消息
              for (let i = 0, len = this.messageList.length; i < len; i++) {
                const currItem = this.messageList[i]

                // 模拟的消息是没有id的，接口响应的数据是有id
                if (currItem.id === undefined) {
                  const msgBody = currItem.msg_body

                  // txt、img是否有值，且与获取到的对应值相等
                  if ((msgBody.content !== undefined && msgBody.content === item.msg_body.content) ||
                    (msgBody.url !== undefined && msgBody.url === item.msg_body.url)) {
                    // 保留发送消息时原有的时间点
                    item.msg_time = currItem.msg_time

                    // 保留图片的自定义imgUuid等字段
                    if (msgBody.url) {
                      item.imgUuid = currItem.imgUuid
                    }
  
                    this.messageList.splice(i, 1, item)
                    messageList[index] = undefined

                    break
                  }
                }
              }
            })

            const temp = []

            // 过滤掉自己模拟的消息
            messageList.forEach(item => {
              if (item !== undefined) temp.push(item)
            })

            // 更新左侧用户列表中被选中用户的消息
            if ((CALL_TYPE_FIRST_REQUEST === params.call_type || CALL_TYPE_NEW_MSG === params.call_type) &&
              temp.length > 0) {
              const item = temp[temp.length - 1]

              ;[this.FIELD_RETAILER_USER, this.FIELD_SUPPLIER_USER].forEach(key => {
                const user = this.readConversationList[key][0]

                if (this.activeUid === user.uid) {
                  if (item.msg_type === this.TYPE_MSG_TXT_STR) {
                    user.newest_msg.msg_body.content = item.msg_body.content
                  } else if (data.type === this.TYPE_MSG_IMG_STR) {
                    user.newest_msg.msg_body.content = this.DEFAULT_IMG_TIPS
                  }
                }
              })
            }

            // 历史消息放到队列前面
            if (CALL_TYPE_HISTORY_MSG === params.call_type) {
              // 图片预览集合
              const copy = [...temp]

              copy.reverse().forEach((item, index) => {
                if (item.msg_body.url !== undefined) {
                  const imgUuid = generateUuid()
                  const imgInfo = {
                    path_full: item.msg_body.url,
                    width: item.msg_body.width,
                    height: item.msg_body.height,
                    imgUuid,
                  }

                  temp[temp.length - index - 1].imgUuid = imgUuid
                  this.imgList.unshift(imgInfo)
                }
              })

              this.messageList = temp.concat(this.messageList)
            } else {
              temp.forEach(item => {
                if (item.msg_body.url !== undefined) {
                  const imgUuid = generateUuid()
                  const imgInfo = {
                    path_full: item.msg_body.url,
                    width: item.msg_body.width,
                    height: item.msg_body.height,
                    imgUuid,
                  }

                  item.imgUuid = imgUuid
                  this.imgList.push(imgInfo)
                }
              })

              this.messageList = this.messageList.concat(temp)
            }

            // 在轮询新消息时，如果滚动条在消息区域底部附近，则将滚动条置到最底部
            if (CALL_TYPE_NEW_MSG === params.call_type && temp.length > 0) {
              const container = this.$refs.dialogBox
              const scrollHeight = container.scrollHeight
              const offsetHeight = container.offsetHeight
              const scrollTop = container.scrollTop
              const distance = 160

              // 滚动到消息列表底部
              if (scrollHeight - offsetHeight - scrollTop < distance) {
                this.$nextTick(() => {
                  this.scrollToBottom()
                })
              } else {
                // 当前被选中的用户需要显示"红点"
                this.activeUserNewMsgCount += temp.length
              }
            }
          })
          .catch(err => {
            this.$message.warning(err)
          })
          .finally(() => {
            finallyFunc()
          })
      },

      // 发送消息
      pushMsg(data) {
        api.pushMsg(data)
          .then(response => {
            // 将发送成功的数据更新到左侧被选中的"已读"用户消息中
            // 将左侧已选中的"已读"用户提升到"已读"列表顶部
            this.readConversationList[this.activeTab].forEach((item, readIndex) => {
              if (this.activeUid === item.uid) {
                const user = this.readConversationList[this.activeTab][readIndex]

                // 更新左侧用户的消息记录
                if (data.type === this.TYPE_MSG_TXT) {
                  user.newest_msg.msg_body.content = data.content
                } else if (data.type === this.TYPE_MSG_IMG) {
                  user.newest_msg.msg_body.content = this.DEFAULT_IMG_TIPS
                }

                this.readConversationList[this.activeTab].splice(readIndex, 1)
                this.readConversationList[this.activeTab].unshift(user)
              }
            })

            // 左侧用户列表滚动到最顶部
            this.$refs.userList.scrollTop = '0px'

            // 发送消息成功，改变消息状态
            this.messageList.forEach(item => {
              if (item.uuid === data.uuid) {
                item.msg_push_status = 'success'
              }
            })
          })
          .catch(err => {
            // 发送消息失败
            this.$message.warning(`发送失败${err}`)

            // 改变消息状态
            this.messageList.forEach(item => {
              if (item.uuid === data.uuid) {
                item.msg_push_status = 'warning'
              }
            })
          })
          .finally(() => {
            //
          })

        this.$nextTick(() => {
          // 右侧历史消息框滚动到最底部
          this.scrollToBottom()
        })
      },

      // 文本框"发送"事件监听器
      sendTxtListener() {
        this.message = this.message.trim()

        if (!this.contactAvailable) {
          this.$message.warning(CONFLICT_TIPS)

          return
        }

        // 使输入框获得焦点
        this.$refs.textarea.focus()

        if (this.message === '') {
          this.$message.warning('不能发送空消息')

          return
        }

        const msgPushStatus = 'loading'
        const uuid = generateUuid()

        const data = {
          recv_uid: this.activeUid,
          type: TYPE_MSG_TXT,
          contact_id: this.contactId,
          content: this.message.trim(),
          msg_push_status: msgPushStatus,
          uuid,
        }

        const msg = {
          from_id: Date.now(),
          msg_time: timeFormat(new Date()),
          msg_type: TYPE_MSG_TXT_STR,
          msg_body: {
            content: data.content,
          },
          msg_push_status: msgPushStatus,
          uuid,
        }

        // 将模拟数据加入右侧的历史消息列表
        this.messageList.push(msg)
        this.message = ''

        this.pushMsg(data)
      },

      // 在发送图片前进行拦截
      sendImgIntercept(event) {
        if (this.contactAvailable) return true

        event.preventDefault()
        event.stopPropagation()

        this.$message.warning('请选择有效用户')

        return false
      },

      // 图片"发送"逻辑
      sendImg(imgInfo) {
        if (!this.contactAvailable) return

        const msgPushStatus = 'loading'
        const uuid = generateUuid()

        const data = {
          recv_uid: this.activeUid,
          type: TYPE_MSG_IMG,
          contact_id: this.contactId,
          image: {
            url: imgInfo.path_full,
            width: imgInfo.width,
            height: imgInfo.height,
          },
          msg_push_status: msgPushStatus,
          uuid,
        }

        // 展示图片时，样式是固定宽度
        let width = data.image.width
        let height = data.image.height

        if (data.image.width > IMG_MAX_WIDTH) {
          width = IMG_MAX_WIDTH
          height = (IMG_MAX_WIDTH / data.image.width) * data.image.height
        }

        const msg = {
          from_id: Date.now(),
          msg_time: timeFormat(new Date()),
          msg_type: TYPE_MSG_IMG_STR,
          msg_body: {
            url: data.image.url,
            width,
            height,
          },
          msg_push_status: msgPushStatus,
          path_full: data.image.url,
          imgUuid: generateUuid(),
          width,
          height,
          uuid,
        }

        // 将模拟数据加入右侧的历史消息列表
        this.messageList.push(msg)
        this.imgList.push(msg)

        this.pushMsg(data)
      },

      // 计算聊天框高度，使"发送区"能覆盖在"消息列表"最底部
      // 计算左侧用户列表高度，超出屏幕高度时滚动查看
      recalculateHeight() {
        // 聊天框距离页面顶部高度
        const dialogueTop = getTopPosition(this.$refs.dialogueBody)
        const winHeight = win.innerHeight
        const sandboxHeight = this.$refs.sendbox.offsetHeight
        const extraHeight =
          parseInt(getComputedStyle(doc.getElementsByClassName('service-page-body')[0])['margin-bottom'], 10)
        const distance = winHeight - dialogueTop - extraHeight
        const dialogueBodyHeight = distance > sandboxHeight ? distance : sandboxHeight

        this.$refs.dialogueBody.style.height = `${dialogueBodyHeight}px`
        this.$refs.dialogBox.style.height = `${dialogueBodyHeight - sandboxHeight}px`

        const userTop = getTopPosition(this.$refs.userList)
        const userHeight = winHeight - userTop - extraHeight
        const searchTop = getTopPosition(this.$refs.searchList)
        const searchHeight = winHeight - searchTop - extraHeight

        this.$refs.userList.style.height = `${userHeight}px`
        this.$refs.searchList.style.height = `${searchHeight}px`
      },

      // 消息列表框滚动到底部
      scrollToBottom() {
        this.$refs.dialogBox.scrollTop = this.$refs.dialogBox.scrollHeight

        // 当前被选中的用户，"未读"新消息数重置
        this.activeUserNewMsgCount = 0
      },

      handleWindowResizeEvent(flag = true) {
        const action = flag ? 'addEventListener' : 'removeEventListener'

        // 动态添加resize事件监听器
        if (!this.onresizeListener) {
          this.onresizeListener = debounce(() => {
            this.recalculateHeight()
          }, 300)
        }

        win[action]('resize', this.onresizeListener)
      },

      // 右侧历史消息列表区域绑定事件
      handleDialogScrollEvent() {
        this.$refs.dialogBox.addEventListener('scroll', this.onscrollDialogListener)
      },

      // 右侧历史消息列表scroll监听器
      onscrollDialogListener(event) {
        // 消息框容器、高端、滚动top
        const container = this.$refs.dialogBox
        const originalHeight = container.scrollHeight
        const offsetHeight = container.offsetHeight
        const scrollTop = container.scrollTop

        // 如果滚动到底部，则将当前被选中用户的未读数置为0 TODO
        if (this.scrollLoading === false && originalHeight - offsetHeight - scrollTop < this.activeUserNewMsgCount * 70) {
          this.activeUserNewMsgCount = 0
        }

        if (scrollTop > 0) return

        if (this.scrollLoading) {
          event.preventDefault()
          event.stopPropagation()

          return
        }

        this.scrollLoading = true

        // 拉取消息记录
        const params = {
          recv_uid: this.activeUid,
          contact_id: this.contactId,
          call_type: CALL_TYPE_HISTORY_MSG,
          id: this.historyId,
          count: PAGE_COUNT,
          _type: 'scroll',
        }

        this.pullMsg(params, () => {
          this.$nextTick(() => {
            // 滚动到原来的位置----刚获取到的历史消息与现有的消息视图之间
            container.scrollTop = container.scrollHeight - originalHeight
            this.scrollLoading = false
          })
        })
      },

      handleUploadEvent() {
        // 初始化上传
        const uploadSetting = {
          browse_button: 'upload-trigger',
          container: 'upload-area',
          postfiles_button: 'upload-files',
          path_name: 'privatemsg',
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
            // this.$message.success('上传成功')

            this.sendImg(data)
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

      // 轮询左侧列表--未读消息用户
      loopUnread() {
        this.loopUnreadTimer = setTimeout(() => {
          this.unread(() => {
            this.loopUnread()
          })
        }, LOOP_INTERVAL_TIME)
      },

      loopNewMsg() {
        const params = {
          recv_uid: this.activeUid,
          contact_id: this.contactId,
          call_type: CALL_TYPE_NEW_MSG,
          id: this.newId,
        }

        this.loopNewMsgTimer = setTimeout(() => {
          // 未选中用户
          if (!this.contactAvailable || this.contactId !== params.contact_id) return

          // 若当前用户无任何消息判断
          if (!this.newId) {
            params.call_type = CALL_TYPE_FIRST_REQUEST

            delete params.id
          }

          this.pullMsg(params, () => {
            this.loopNewMsg()
          })
        }, LOOP_INTERVAL_TIME)
      },

      // "供"、"销"端tab切换
      tabSwitchListener(tab, event) {
        this.activeTab = tab

        // 重置"未读"消息数
        this.inactiveTabNewMsgCount = 0
      },

      // 关闭窗口---释放会话
      handleWindowCloseEvent() {
        window.onunload = () => {
          this.releaseMsgContact()
        }
      },

      emitPreview(imgUuid) {
        let indexImg = 0

        this.imgList.forEach((item, index) => {
          if (imgUuid === item.imgUuid) {
            indexImg = index
          }
        })

        // 触发子组件预览方法
        this.$refs.picturePreview.showBigPicturePreview(indexImg)
      },

      // 搜索"回车"事件
      enterSearch() {
        if (this.search === '') return

        this.searchStart = 0
        this.searchUuid = Date.now()
        this.noMoreSearch = false
        this.searchList = []

        const params = {
          start: this.searchStart,
          keywords: this.search,
          uuid: this.searchUuid,
          param_type: '',
        }

        this.searchMsg(params)
      },

      searchMsg(params) {
        if (params.param_type === 'scroll' && this.noMoreSearch) return

        api.searchMsg(params)
          .then(response => {
            if (this.searchUuid !== params.uuid) return

            const chatList = response.data.chat_list || []
            const searchList = []

            if (chatList.length < PAGE_COUNT) this.noMoreSearch = true

            // 头像、昵称等字段提取
            chatList.forEach(item => {
              const temp = {}

              if (this.customer_service.service_id === item.recv_uid) {
                temp.uid = item.send_uid
                temp.avatar = IMG_DOMAIN + item.send_avatar
                temp.name = item.send_name
              } else if (this.customer_service.service_id === item.send_uid) {
                temp.uid = item.recv_uid
                temp.avatar = IMG_DOMAIN + item.recv_avatar
                temp.name = item.recv_name
              }

              temp.msg = item.msg

              if (item.user_type === this.FIELD_RETAILER_INT) {
                temp.user_type = this.FIELD_RETAILER_USER
              } else if (item.user_type === this.FIELD_SUPPLIER_INT) {
                temp.user_type = this.FIELD_SUPPLIER_USER
              }

              searchList.push(temp)

              this.$nextTick(() => {
                this.recalculateHeight()
              })
            })

            if (params.param_type === 'scroll') {
              this.searchList = this.searchList.concat(searchList)
            } else {
              this.searchList = searchList
            }
          })
          .catch(err => {
            if (!this.searchFocus) return

            this.$message.warning(`搜索失败：${err}`)
          })
          .finally(() => {
            // 左侧搜索结果区域滚动状态重置
            this.scrollSearchLoading = false
          })
      },

      focusSearch() {
        this.searchFocus = true
        this.searchUuid = Date.now()
        this.outSearchListFlag = true
      },

      blurSearch() {
        if (this.outSearchListFlag) {
          this.resetSearchState()
        }
      },

      // 在搜索结果列表上移动
      overSearchList() {
        this.outSearchListFlag = false
      },

      outSearchList() {
        this.outSearchListFlag = true
      },

      clickSearchList() {
        this.resetSearchState()
      },

      // 重置搜索相关的属性
      resetSearchState() {
        this.search = ''
        this.searchFocus = false
        this.searchStart = 0
        this.searchUuid = Date.now()
        this.outSearchListFlag = true
        this.scrollSearchLoading = false
        this.noMoreSearch = false
        this.searchList = []

        this.$nextTick(() => {
          this.recalculateHeight()
        })
      },

      handleSearchScrollEvent() {
        this.$refs.searchList.addEventListener('scroll', this.onscrollSearchListener)
      },

      onscrollSearchListener() {
        // 消息框容器、高端、滚动top
        const container = this.$refs.searchList
        const scrollHeight = container.scrollHeight
        const offsetHeight = container.offsetHeight
        const scrollTop = container.scrollTop

        // 滚动到列表底部
        if (scrollHeight - offsetHeight - scrollTop > 0) return

        if (this.scrollSearchLoading) return

        this.scrollSearchLoading = true

        this.searchStart = this.searchStart + PAGE_COUNT

        const params = {
          start: this.searchStart,
          keywords: this.search,
          uuid: this.searchUuid,
          param_type: 'scroll',
        }

        this.searchMsg(params)
      },
    },

    computed: {
      unreadList() {
        return this.unreadConversationList[this.activeTab] || []
      },

      readList() {
        return this.readConversationList[this.activeTab] || []
      },
    },

    components: {
      'main-header': MainHeader,
      picturePreview,
    },
  }

  export default service
</script>

<style lang="scss">
  // 左侧搜索框
  .search {
    display: flex;
    height: 50px;
    justify-content: center;
    align-items: center;

    .el-input {
      width: 268px;
      height: 30px;
      // border: 1px solid #d1d1d1;

      .el-input__inner {
        border-radius: 4px;
      }
    }

    .el-icon-search {
      line-height: 30px;
    }
  }

  // 左侧用户列表里的消息红点
  .red-hot {
    height: 14px;

    &>.el-badge__content {
      top: -4px;
      height: 14px;
      padding: 0 2px;
      font-size: 12px;
      line-height: 12px;
      background-color: #cdae63;
    }
  }

  .tabs-item {
    &>.red-hot {
      left: 2px;
      top: -5px
    }
  }
</style>

<style lang="scss" scoped>
  @import "./service";
</style>
