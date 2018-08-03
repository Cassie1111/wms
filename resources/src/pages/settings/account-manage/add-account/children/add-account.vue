<template>
  <div class="page">
    <div class="location">
      <span>设置</span>
      <span> / </span>
      <span>子账号管理</span>
      <span> / </span>
      <span v-if="isEdit">编辑员工</span>
      <span v-else>新建员工</span>
    </div>

    <div class="page-content">
      <div class="form">
        <div class="form-title" v-if="isEdit">编辑员工账号</div>
        <div class="form-title" v-else>新建员工账号</div>
        <div class="form-body">
          <el-form ref="form" label-position="right" :model="form" :rules="rules">
            <div class="block-label">基本信息：</div>
            <div class="block-content">
              <el-form-item label="账号名" prop="username">
                <el-input v-model.trim="form.username" :disabled="isEdit">
                  <template slot="prepend">{{mainUsername}}:</template>
                </el-input>
                <div class="tips">
                  <div class="triangle"></div>
                  <div class="text">当前子账号不支持账号名修改</div>
                </div>
              </el-form-item>
              <el-form-item label="登陆密码" prop="password" required>
                <input name="username" type="text" :value="fullUsername" style="display:none">
                <el-input class="password" type="text" ref="pwd1" v-model="form.password"></el-input>
              </el-form-item>
              <el-form-item label="确认密码" prop="conPassword" required>
                <el-input class="password" type="text" ref="pwd2" v-model="form.conPassword"></el-input>
              </el-form-item>
              <el-form-item label="姓名" prop="realName">
                <el-input v-model.trim="form.realName"></el-input>
              </el-form-item>
              <el-form-item label="部门" prop="department">
                <el-autocomplete
                  v-model.trim="form.department"
                  :fetch-suggestions="querySearchDepartment"
                  placeholder="请输入部门">
                </el-autocomplete>
              </el-form-item>
              <el-form-item label="职务" prop="duty">
                <el-autocomplete
                  class="inline-input"
                  v-model.trim="form.duty"
                  :fetch-suggestions="querySearchDuty"
                  placeholder="请输入职务">
                </el-autocomplete>
              </el-form-item>
            </div>

            <div class="block-label">安全信息：</div>
            <div class="block-content">
              <el-form-item label="员工手机号">
                <el-form-item class="country-code">
                  <el-select  v-model="form.countryCode" placeholder="请选择区号">
                    <el-option
                      v-for="(item, index) in countryCodeList"
                      :key="index"
                      :label="`${item.countrycode} ${item.countryname}`"
                      :value="item.countrycode">
                    </el-option>
                  </el-select>
                </el-form-item>

                <el-form-item class="mobile" prop="mobile">
                  <el-input v-model.trim="form.mobile"></el-input>
                </el-form-item>

                <div class="tips">
                  <div class="triangle"></div>
                  <div class="text">可用于子账号密码找回等操作</div>
                </div>
              </el-form-item>

              <el-form-item label="邮箱" prop="email">
                <el-input v-model.trim="form.email"></el-input>
              </el-form-item>
              <el-form-item label="身份证" prop="identity">
                <el-input v-model.trim="form.identity"></el-input>
              </el-form-item>
              <el-form-item class="permission" label="权限管理" prop="permission">
                <el-tree
                  :data="perTree"
                  show-checkbox
                  node-key="id"
                  ref="tree"
                  :props="defaultProps">
                </el-tree>
              </el-form-item>
            </div>
          </el-form>

          <div class="form-btn-group">
            <el-button class="submit-btn" type="primary" size="medium" @click="submit">
              <span v-if="isEdit">确认编辑</span>
              <span v-else>确认新建</span>
            </el-button>
            <el-button plain class="cancel-btn" size="medium" @click="cancel">取消</el-button>
          </div>
        </div>
      </div>
    </div>
    <el-dialog
      title="确定取消吗？"
      :visible.sync="dialogVisible"
      width="300px">
      <span>点击"确定"，您的记录将不会被保存</span>
      <span slot="footer" class="dialog-footer">
        <el-button size="mini" @click="dialogVisible = false">取 消</el-button>
        <el-button size="mini" type="primary" @click="handleConfirm">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
  import * as accountApi from '@/api/settings/add-account'
  import { getAccountList } from '@/api/settings/account-list'
  import { Message } from 'element-ui'
  import permissionTree from './permission-tree'
  import formRules from './form-rules'

  export default {
    name: 'add-account',

    data() {
      formRules.password[1] = {
        validator: this.validatePass,
        trigger: 'blur',
      }

      formRules.conPassword[1] = {
        validator: this.validatePass2,
        trigger: 'blur',
      }

      formRules.permission = [
        {
          validator: this.validatePermission,
          trigger: 'blur',
        },
      ]

      return {
        mainUsername: '迷橙买手宝',
        isEdit: this.$route.name !== 'add-account' || false,
        departmentList: [],
        dutyList: [],
        countryCodeList: [],
        form: {
          username: '',
          password: '',
          conPassword: '',
          realName: '',
          department: '',
          duty: '',
          countryCode: '',
          mobile: '',
          email: '',
          identity: '',
        },
        rules: formRules,
        perTree: permissionTree,
        defaultProps: {
          label: 'name',
          children: 'children',
        },
        dialogVisible: false,
      }
    },

    beforeRouteEnter(to, from, next) {
      if (to.name === 'add-account') {
        // 获取账号列表 统计数量，最多新建50个
        getAccountList().then(res => {
          const accountTotal = res.data.user_list.length

          if (accountTotal >= 50) {
            next({
              name: 'account-list',
            })
          } else {
            next()
          }
        })
      } else {
        next()
      }
    },

    created() {
      this.$nextTick(() => {
        console.log(this.$refs.pwd1)
        const pwd1Input = this.$refs.pwd1.$el.children[0]
        const pwd2Input = this.$refs.pwd2.$el.children[0]
        const style = window.getComputedStyle(pwd1Input)

        if (!style.webkitTextSecurity) {
          // Do nothing
          pwd1Input.type = 'password'
          pwd2Input.type = 'password'
        }
      })
    },

    mounted() {
      this.setDepartmentList()
      this.setDutyList()
      this.setCountryCodeList()

      if (this.$route.name === 'edit-account') {
        accountApi.getAccountInfo(this.$route.params.userNo).then(res => {
          this.setFormData(res.data.user_detail)
        })
      }
    },

    computed: {
      fullUsername() {
        return `${this.mainUsername}:${this.form.username}`
      },
    },

    methods: {
      validatePass(rule, value, callback) {
        if (value === '') {
          callback(new Error('请输入密码'))
        } else {
          if (this.form.conPassword !== '') {
            this.$refs.form.validateField('conPassword')
          }
          callback()
        }
      },

      validatePass2(rule, value, callback) {
        if (value === '') {
          callback(new Error('请再次输入密码'))
        } else if (value !== this.form.password) {
          callback(new Error('两次输入密码不一致!'))
        } else {
          callback()
        }
      },

      validatePermission(rule, value, callback) {
        if (this.$refs.tree.getCheckedKeys().length < 1) {
          callback(new Error('请至少选择一个权限'))
        } else {
          callback()
        }
      },

      // 获取部门列表
      setDepartmentList() {
        accountApi.getDepartmentList().then(res => {
          this.departmentList = res.data.department
        })
      },

      // 获取职务列表
      setDutyList() {
        accountApi.getDutyList().then(res => {
          this.dutyList = res.data.duty
        })
      },

      // 获取国家区号列表
      setCountryCodeList() {
        accountApi.getCountryCode().then(res => {
          this.countryCodeList = res.data

          for (let i = 0; i < this.countryCodeList.length; i++) {
            const item = this.countryCodeList[i]

            if (Number(item.countrycode) === Number(this.countryCode) && item.countryname === this.countryName) {
              this.form.countryCode = i
              break
            }
          }
        })
      },

      // 将用户信息填充到表单
      setFormData(data) {
        this.form.username = data.username.indexOf(`${this.mainUsername}:`) === 0
          ? data.username.replace(`${this.mainUsername}:`, '')
          : data.username
        this.form.password = ''
        this.form.conPassword = ''
        this.form.realName = data.realname
        this.form.department = data.department
        this.form.duty = data.duty
        this.form.countryCode = data.country_code
        this.form.mobile = data.mobile_phone
        this.form.email = data.email
        this.form.identity = data.identity_no
        this.$refs.tree.setCheckedKeys(data.permission)
      },

      // 提交员工信息
      submit() {
        this.$refs.form.validate(valid => {
          if (valid) {
            const data = this.getParams()

            const userNo = this.$route.params.userNo

            if (userNo) {
              accountApi.editAccount(userNo, data).then(() => {
                Message({
                  message: '修改成功',
                  type: 'success',
                  duration: 2000,
                })

                setTimeout(() => {
                  this.$router.push({
                    name: 'account-list',
                  })
                }, 2000)
              })
            } else {
              accountApi.addAccount(data).then(() => {
                Message({
                  message: '新建成功',
                  type: 'success',
                  duration: 2000,
                })

                setTimeout(() => {
                  this.$router.push({
                    name: 'account-list',
                  })
                }, 2000)
              })
            }
          }
        })
      },

      // 确认"取消新建"
      handleConfirm() {
        this.dialogVisible = false
        this.$router.push({
          name: 'account-list',
        })
      },

      // 取消新建员工
      cancel() {
        this.dialogVisible = true
      },

      querySearchDepartment(queryString, cb) {
        const departmentList = this.departmentList.map(item => {
          const department = {
            value: item,
          }

          return department
        })
        const results = queryString ? departmentList.filter(this.createFilter(queryString)) : departmentList

        // 调用 callback 返回建议列表的数据
        cb(results)
      },

      querySearchDuty(queryString, cb) {
        const dutyList = this.dutyList.map(item => {
          const duty = {
            value: item,
          }

          return duty
        })
        const results = queryString ? dutyList.filter(this.createFilter(queryString)) : dutyList

        // 调用 callback 返回建议列表的数据
        cb(results)
      },

      createFilter(queryString) {
        return item => item.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0
      },

      getParams() {
        return {
          username: `${this.mainUsername}:${this.form.username}`,
          password: this.form.password,
          realname: this.form.realName,
          department: this.form.department,
          duty: this.form.duty,
          country_code: this.form.countryCode,
          mobile_phone: this.form.mobile,
          email: this.form.email,
          identity_no: this.form.identity,
          permission: this.$refs.tree.getCheckedKeys(),
        }
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import 'add-account';
</style>

<style lang="scss">
  .form-body {
    .el-form-item {
      margin-bottom: 15px;

      .el-form-item__label {
        width: 100px;
      }
      .el-form-item__content {
        height: 30px;
        margin-left: 130px;

        .el-input {
          width: 394px;

          &-group__prepend {
            border-radius: 0;
          }
        }

        .el-form-item__error {
          padding-top: 2px;
        }
      }
    }

    .password {
      .el-input__inner {
        -webkit-text-security:disc;
      }
    }

    .permission {
      .el-form-item__content {
        height: auto;

        .el-tree {
          width: 394px;

          &-node__label {
            color: #666;
          }
        }
      }
    }

    // 区号
    .country-code {
      .el-form-item__content {
        margin-left: 0px;

        .el-select {
          width: 150px;

          .el-input {
            width: 150px;

            .el-input__suffix {
              right: 0px;
              background: rgba(209,209,209,1);

              .el-input__icon {
                width: 30px;
                color: #fff;
              }
            }
          }

        }
      }
    }

    // 手机号
    .mobile {
      .el-form-item__content {
        margin-left: 0px;
      }

      .el-input {
        width: 223px !important;
      }
    }
  }
</style>
