<template>
  <div class="pusher">
    <el-form class="form-area" :model="ruleForm" :rules="rules" ref="ruleForm">
      <div class="form-area-item">
        <div class="form-area-item-nav">推送时间</div>
        <div class="form-area-item-content">
          <div class="time-part">
            <el-form-item>
              <el-date-picker
                v-model="ruleForm.date" @change="getDate" :picker-options="pickerOptions"
                value-format="yyyy-MM-dd" placeholder="选择日期"></el-date-picker>
            </el-form-item>
            <el-form-item style="margin-left: 30px">
              <el-time-picker v-model="ruleForm.time" @change="getTime" :picker-options="pickerOptions"
                              value-format="HH:mm:ss" placeholder="选择时间"></el-time-picker>
            </el-form-item>
          </div>
          <div v-if="type === 2" class="user">
            <div class="user-header">
              <span :class="{active: isAllUser}" @click="toggleUser(true)">全部用户</span>
              <span :class="{active: !isAllUser}" @click="toggleUser(false)">指定用户</span>
            </div>
            <div class="user-content">
              <el-form-item prop="userList">
                <el-input v-model="ruleForm.userList" type="textarea" :row="6"
                          :disabled="isAllUser" :placeholder="smsUserText"></el-input>
              </el-form-item>
            </div>

          </div>
        </div>
      </div>
      <div class="form-area-item">
        <div class="form-area-item-nav">推送消息</div>
        <div class="form-area-item-content">
          <el-form-item prop="content">
            <el-input type="textarea" :rows="6" v-model="ruleForm.content" placeholder="填写要推送的内容..."></el-input>
          </el-form-item>
          <div class="operate-area">
            <el-button type="primary" size="small" @click="send">发送</el-button>
          </div>
        </div>
      </div>
      <div class="form-area-item">
        <div class="form-area-item-nav">历史推送</div>
      </div>
    </el-form>

    <div class="pusher-table">
      <el-table v-loading="loading"
                :data="pushList">
        <el-table-column
          prop="content"
          label="推送内容"
          width="420"
          align="center">
        </el-table-column>
        <el-table-column
          prop="send_time"
          label="推送时间"
          width="220"
          align="center">
        </el-table-column>
        <el-table-column
          prop="create_time"
          label="创建时间"
          width="220"
          align="center">
        </el-table-column>
      </el-table>
      <div v-if="total > 20" class="pagination">
        <el-pagination
          background
          @current-change="handleCurrentChange"
          :current-page.sync="page"
          :page-size="20"
          layout="prev, pager, next, jumper"
          :total="total">
        </el-pagination>
      </div>
    </div>
  </div>
</template>
<script>
import { getList, sendMsg } from '@/api/marketing/pusher'

export default {
  name: 'pusher-list',
  props: ['type'],

  data() {
    return {
      pushList: [],
      page: 1,
      total: 0,
      loading: true,
      isAllUser: true,
      smsUserText: '对全部用户发送',
      tip: '操作成功',
      ruleForm: {
        content: '',
        date: '',
        time: '',
        userList: '',
      },
      pickerOptions: {
        disabledDate(time) {
          return time.getTime() < Date.now() - 8.64e7
        },
      },
      rules: {
        date: [
          {
            type: 'string',
            required: true,
            message: '请选择日期',
            trigger: 'change',
          },
        ],
        time: [
          {
            type: 'string',
            required: true,
            message: '请选择时间',
            trigger: 'change',
          },
        ],
        content: [
          {
            required: true,
            message: '请填写要推送的内容',
            trigger: 'blur',
          },
          {
            max: 240,
            message: '最大长度为240个字符',
            trigger: 'blur',
          },
        ],
        userList: [
          {
            required: false,
            message: '请填写指定用户',
            trigger: 'blur',
          },
        ],
      },
    }
  },

  mounted() {
    this.getPusherList()
  },
  methods: {
    toggleUser(val) {
      if (val) {
        this.smsUserText = '对全部用户发送'
      } else {
        this.smsUserText = '对指定用户发送'
      }
      this.isAllUser = val
      this.ruleForm.userList = ''

      this.rules.userList[0].required = !val
    },
    getDate(val) {
      this.ruleForm.date = val
    },
    getTime(val) {
      this.ruleForm.time = val
    },
    handleCurrentChange(val) {
      this.page = val
      this.loading = true
      this.getPusherList()
    },
    getPusherList() {
      const page = this.page
      const type = this.type
      const options = {
        page,
        type,
      }

      getList(options)
        .then(response => {
          this.pushList = response.data.push_list
          this.total = response.data.total
        })
        .catch(err => {
          // 错误处理
          this.$message.error(err)
        })
        .finally(() => {
          this.loading = false
        })
    },
    resetParams() {
      this.ruleForm.content = ''
      this.ruleForm.date = ''
      this.ruleForm.time = ''
      this.ruleForm.userList = ''
    },
    send() {
      this.$refs.ruleForm.validate(valid => {
        if (!valid) return
        const userList = this.ruleForm.userList.trim().replace(/\n/g, ',').replace(/\s+/g, '')

        this.ruleForm.send_type = this.type
        this.ruleForm.send_uid_list = this.arrayRemoval(userList)
        const data = this.ruleForm

        sendMsg(data)
          .then(response => {
            const tip = this.tip

            this.$message.success(tip)
            setTimeout(() => {
              this.resetParams()
              this.getPusherList()
            }, 2000)
          })
          .catch(err => {
          // 错误处理
            this.$message.error(err)
          })
      })
    },
    arrayRemoval(str) {
      const arr = Array.from(new Set(str.split(',')))

      return arr.join(',')
    },
  },
}
</script>
<style scoped lang="scss">
  .form-area {
    &-item {
      padding: 0 0 20px;
      &-nav {
        width: 100%;
        height: 40px;
        padding: 0 0 0 24px;
        margin: 0 0 30px;
        line-height: 40px;
        background-color: #F6F6F6;
      }
      &-content {
        padding: 0 0 0 30px;
        .time-part {
          display: flex;
        }
        .user {
          &-header {
            span:last-child {
              padding: 0 0 0 40px;
            }
            span:hover {
              color: #cdae63;
              cursor: pointer;
            }
            .active {
              color: #cdae63;
            }
          }
          &-content {
            padding: 20px 0 0;
          }
        }
      }
      .operate-area {
        display: flex;
        justify-content: flex-end;
        padding: 20px 420px 0 0;
      }
    }
  }
  .pusher-table {
    margin: -30px 0 0;
  }
</style>
<style lang="scss">
  .pusher {
    .el-input__icon {
      line-height: 30px;
    }
    .el-date-editor {
      width: 200px !important;
    }
    .el-textarea__inner {
      width: 430px;
    }
    .el-button--small {
      width: 100px;
    }
    .user-content {
      .el-textarea__inner {
        height: 100px;
      }
    }
    .pagination {
      padding: 20px 0;
      text-align: center;
    }
  }
</style>