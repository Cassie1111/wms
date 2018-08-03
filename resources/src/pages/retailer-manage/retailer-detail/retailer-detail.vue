<template>
  <div class="page">
    <div class="location">
      <span>分销商管理</span>
      <span>/</span>
      <span>{{ title }}</span>
    </div>
    <section class="page-content retailer-detail">
      <div class="title">{{ title }}</div>
      <div class="form-area">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px">
          <div class="form-area-item">
            <div class="form-area-item-nav">联系人</div>
            <el-form-item label="联系人姓名" prop="retailer_name">
              <el-input v-model.trim="ruleForm.retailer_name"></el-input>
            </el-form-item>
            <el-form-item label="联系人电话" prop="contact_phone_number">
              <el-input v-model.trim="ruleForm.contact_phone_number"></el-input>
            </el-form-item>
            <el-form-item label="联系人邮箱" prop="contact_email_address">
              <el-input v-model.trim="ruleForm.contact_email_address"></el-input>
            </el-form-item>
          </div>
          <div class="form-area-item">
            <div class="form-area-item-nav">帐号信息</div>
            <el-form-item class="phone-area" label="账号手机" required>
              <el-form-item class="country-code" prop="country_code">
                <el-select v-model="ruleForm.country_code">
                  <el-option
                    v-for="(item,index) in countryCodeList"
                    :key="index"
                    :label="item.countrycode+' '+item.countryname"
                    :value="item.countrycode">
                  </el-option>
                </el-select>
              </el-form-item>
              <el-form-item class="phone-num" prop="mobile_account">
                <el-input v-model.trim="ruleForm.mobile_account"></el-input>
              </el-form-item>
            </el-form-item >
            <el-form-item label="登录密码">
              <span class="pwd-tip">初始密码为手机号后6位</span>
              <el-button v-if="flag === 'edit' &&
              (accountType || retailer.length && retailer[0]['operator_types'].includes(1))"
                         type="primary" :disabled="disabled"
                         size="mini" @click="resetPwd">重置密码</el-button>
            </el-form-item>
          </div>
        </el-form>
      </div>
      <div v-if="accountType || retailer.length && retailer[0]['operator_types'].includes(1)"
           class="operate-area">
        <el-button v-if="flag === 'add'" type="primary"
                   class="btn-primary" size="small" @click="onSubmit('add')">添加</el-button>
        <el-button v-if="flag === 'edit'" type="primary" size="small" :disabled="disabled"
                   @click="onSubmit('edit')">编辑</el-button>
        <el-button v-if="flag === 'edit'" type="info" size="small" :disabled="disabled"
                   class="btn-info" @click="onSubmit('delete')">删除</el-button>
      </div>
    </section>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex'
  import { getDetail, editRetailer } from '@/api/retailer/retailer'
  import { getCountryCode } from '@/api/supplier/supplier'

  export default {
    name: 'add-retailer',
    data() {
      return {
        flag: 'add',
        title: '添加分销商',
        tips: '',
        disabled: false,
        countryCodeList: [],
        ruleForm: {
          retailer_name: '',
          contact_phone_number: '',
          contact_email_address: '',
          retailer_no: '',
          country_code: '',
          mobile_account: '',

          // 非必填，只有点击重置的时候给接口传过去
          password: '',
        },
        rules: {
          retailer_name: [
            {
              required: true,
              message: '请填入分销商名称',
              trigger: 'blur',
            },
          ],
          contact_phone_number: [
            {
              required: true,
              message: '请填写联系人电话',
              trigger: 'blur',
            },
            {
              pattern: /^\d{5,13}$/,
              message: '请填写正确手机号',
              trigger: 'blur',
            },
          ],
          contact_email_address: [
            {
              required: true,
              message: '请填写联系人邮箱',
              trigger: 'blur',
            },
            {
              type: 'email',
              message: '请输入正确的邮箱地址',
              trigger: 'blur',
            },
          ],
          country_code: [
            {
              required: true,
              message: '请选择国家区号',
              trigger: 'change',
            },
          ],
          mobile_account: [
            {
              required: true,
              message: '请填写账号手机',
              trigger: 'blur',
            },
            {
              pattern: /^\d{5,13}$/,
              message: '请填写正确手机号',
              trigger: 'blur',
            },
          ],
        },
      }
    },
    created() {
      // 根据当前页面路由参数判断是编辑or新建
      const id = this.$route.params.id

      if (id !== '0') {
        this.flag = 'edit'
        this.title = '编辑分销商'
        getDetail(id)
          .then(response => {
            const data = response.data.retailer_detail

            if (data.status === 0) {
              this.disabled = true
            }

            this.ruleForm = {
              ...data,
            }
          })
          .catch(err => {
            // 错误处理
            this.$message.error(err)
          })
      }
      this.getCountryCode()
    },
    computed: {
      ...mapGetters(['accountType', 'retailer']),
    },
    methods: {
      getCountryCode() {
        // 获取国家区号
        getCountryCode().then(res => {
          this.countryCodeList = res.data
        }).catch(err => {
          console.log(err)
        })
      },
      resetPwd() {
        if (this.ruleForm.mobile_account === '') {
          this.$message.error('填入帐号手机后才可进行重置哟～')
        } else {
          const phone = this.ruleForm.mobile_account

          this.ruleForm.password = phone.substr(phone.length - 6)
          this.$alert('重置后的密码为账号手机后6位，请点击编辑进行保存', '提示', {
            confirmButtonText: '确定',
          })
        }
      },
      onSubmit(status) {
        this.$refs.ruleForm.validate(valid => {
          if (!valid) return
          if (this.flag === 'add') {
            const phone = this.ruleForm.mobile_account

            this.ruleForm.password = phone.substr(phone.length - 6)
          }
          if (status === 'edit') {
            this.ruleForm.status = 1
            this.tips = '编辑成功'
          } else if (status === 'delete') {
            this.ruleForm.status = 0
            this.tips = '删除成功'
          } else if (status === 'add') {
            this.tips = '添加成功'
          }

          const data = this.ruleForm

          editRetailer(data)
            .then(response => {
              this.$message.info(this.tips)
              setTimeout(() => {
                this.$router.push({
                  path: `/retailer`,
                })
              }, 1500)
            })
            .catch(err => {
              // 错误处理
              this.$message.error(err)
            })
        })
      },
    },
  }
</script>
<style scoped lang="scss">
@import "retailer-detail";
</style>
<style lang="scss">
  .retailer-detail {
    .form-area {
      .el-form-item {
        margin-bottom: 10px;
        &__label {
          padding: 0 0 0 20px;
        }
        &__content {
          display: flex;
          padding: 0 0 0 80px;
        }
        &__error {
          padding: 10px;
          position: static;
        }
        .el-input {
          width: 300px;
        }
      }
      .phone-area {
        .el-form-item__content {
          font-size: 0px;
          padding: 0;

          .el-form-item {
            display: inline-block;
            padding: 0px;
          }

          .country-code {
            width: 124px;
            margin:0 6px 0 80px;
            .el-select {
              width: 124px;
              .el-input {
                width: 124px;
              }
            }
            .el-form-item__error {
              position: absolute;
            }
          }

          .phone-num {
            .el-input {
              width: 170px;
            }
          }
        }
      }
    }

    .operate-area {
      .el-button--mini,
      .el-button--small {
        width: 100px;
      }
    }
  }
</style>

