<template>
  <div class="page">
    <div class="location">
      <span>供应商管理</span>
      <span>/</span>
      <span>供应商审核</span>
    </div>
    <div class="page-content supplier-detail">
      <div class="form-area">
        <el-form :model="supplierForm" :rules="rules" ref="supplierForm" label-width="150px">
          <div class="form-area-item">
            <div class="form-area-item-nav">基本信息</div>
            <el-form-item label="企业注册地">
              <el-radio v-model="supplierForm.base" label="1" class="mt8">中国大陆</el-radio>
              <el-radio v-model="supplierForm.base" label="2" class="mt8">海外（含港、澳、台）</el-radio>
            </el-form-item>
            <el-form-item label="企业名称" prop="supplier_name">
              <el-input v-model.trim="supplierForm.supplier_name" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="营业执照号" prop="business_license_no">
              <el-input v-model.trim="supplierForm.business_license_no" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
          </div>
          <div class="form-area-item">
            <div class="form-area-item-nav">提交企业资质</div>
            <el-form-item label="营业执照" prop="business_license">
              <picturePreview :pictureList="supplierForm.business_license" @onRemove="handleRemove"></picturePreview>
              <div v-if="accountType || supplier.length && supplier[0]['operator_types'].includes(1)"
                   class="upload-area" id="upload-bus-area" >
                <label id="upload-bus-trigger" for="upload-bus-files">上传照片</label>
                <input id="upload-bus-files" type="file">
              </div>
            </el-form-item>
            <el-form-item label="一般纳税人资格证">
              <picturePreview :pictureList="supplierForm.general_taxpayer_certification"
                              @onRemove="handleRemove"></picturePreview>
              <div v-if="accountType || supplier.length && supplier[0]['operator_types'].includes(1)"
                   class="upload-area" id="upload-tax-area">
                <label id="upload-tax-trigger" for="upload-tax-files">上传照片</label>
                <input id="upload-tax-files" type="file">
              </div>
            </el-form-item>
            <el-form-item label="法人身份证(正反面)" prop="corporate_identity_card">
              <picturePreview :pictureList="supplierForm.corporate_identity_card"
                              @onRemove="handleRemove"></picturePreview>
              <div v-if="accountType || supplier.length && supplier[0]['operator_types'].includes(1)"
                   class="upload-area" id="upload-card-area">
                <label id="upload-card-trigger" for="upload-card-files">上传照片</label>
                <input id="upload-card-files" type="file">
              </div>
            </el-form-item>
            <el-form-item label="银行开户许可证">
              <picturePreview :pictureList="supplierForm.permit_of_opening_a_bank_account"
                              @onRemove="handleRemove"></picturePreview>
              <div v-if="accountType || supplier.length && supplier[0]['operator_types'].includes(1)"
                   class="upload-area" id="upload-bank-area">
                <label id="upload-bank-trigger" for="upload-bank-files">上传照片</label>
                <input id="upload-bank-files" type="file">
              </div>
            </el-form-item>
            <el-form-item label="近一年纳税证明" prop="nearly_one_year_tax_payment_certificate">
              <picturePreview :pictureList="supplierForm.nearly_one_year_tax_payment_certificate"
                              @onRemove="handleRemove"></picturePreview>
              <div v-if="accountType || supplier.length && supplier[0]['operator_types'].includes(1)"
                   class="upload-area" id="upload-payment-area">
                <label id="upload-payment-trigger" for="upload-payment-files">上传照片</label>
                <input id="upload-payment-files" type="file">
              </div>
            </el-form-item>
            <el-form-item label="商标注册证书">
              <picturePreview :pictureList="supplierForm.trademark_registration_certificate"
                              @onRemove="handleRemove"></picturePreview>
              <div v-if="accountType || supplier.length && supplier[0]['operator_types'].includes(1)"
                   class="upload-area" id="upload-trademark-area">
                <label id="upload-trademark-trigger" for="upload-trademark-files">上传照片</label>
                <input id="upload-trademark-files" type="file">
              </div>
            </el-form-item>
            <el-form-item label="链路证明">
              <picturePreview :pictureList="supplierForm.link_certification"
                              @onRemove="handleRemove"></picturePreview>
              <div v-if="accountType || supplier.length && supplier[0]['operator_types'].includes(1)"
                   class="upload-area" id="upload-link-area">
                <label id="upload-link-trigger" for="upload-link-files">上传照片</label>
                <input id="upload-link-files" type="file">
              </div>
            </el-form-item>
            <el-form-item label="开户行名称" prop="opening_bank_name">
              <el-input v-model.trim="supplierForm.opening_bank_name" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="支行名称" prop="subbranch_name">
              <el-input v-model.trim="supplierForm.subbranch_name" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="银行账号" prop="bank_account">
              <el-input v-model.trim="supplierForm.bank_account" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
          </div>
          <div class="form-area-item">
            <div class="form-area-item-nav">联系人</div>
            <el-form-item label="联系人姓名" prop="contact_name">
              <el-input v-model.trim="supplierForm.contact_name" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="联系人电话" prop="contact_phone_number">
              <el-input v-model.trim="supplierForm.contact_phone_number" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="联系人邮箱" prop="contact_email_address">
              <el-input v-model.trim="supplierForm.contact_email_address" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="运营授权证明" prop="operation_authorization_certificate">
              <picturePreview :pictureList="supplierForm.operation_authorization_certificate"
                              @onRemove="handleRemove"></picturePreview>
              <div v-if="accountType || supplier.length && supplier[0]['operator_types'].includes(1)"
                   class="upload-area" id="upload-authorization-area">
                <label id="upload-authorization-trigger" for="upload-authorization-files">上传照片</label>
                <input id="upload-authorization-files" type="file">
              </div>
            </el-form-item>
          </div>
          <div class="form-area-item">
            <div class="form-area-item-nav">仓库地址</div>
            <el-form-item label="所在地" prop="warehouse_location">
              <el-input v-model.trim="supplierForm.warehouse_location" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="仓库联系人" prop="warehouse_contact_name">
              <el-input v-model.trim="supplierForm.warehouse_contact_name" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="仓库联系人电话" prop="warehouse_contact_phone_number">
              <el-input v-model.trim="supplierForm.warehouse_contact_phone_number" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="详细地址" prop="warehouse_detailed_address">
              <el-input v-model.trim="supplierForm.warehouse_detailed_address" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item v-if="supplierForm.status_cn !== 'pendingAudit'" label="配送方式">
              <el-radio v-model="supplierForm.pick_up_type" label="2" class="mt8">送货入仓</el-radio>
              <el-radio v-model="supplierForm.pick_up_type" label="1" class="mt8">上门取货</el-radio>
              <el-radio v-model="supplierForm.pick_up_type" label="3" class="mt8">物流送货</el-radio>
            </el-form-item>
          </div>
          <div class="form-area-item">
            <div class="form-area-item-nav">账号信息</div>
            <el-form-item label="账号名称" prop="account_name">
              <el-input v-model.trim="supplierForm.account_name" placeholder="填写的内容可修改"></el-input>
            </el-form-item>
            <el-form-item label="绑定手机号" class="phone-area" required>
              <el-form-item class="country-code" prop="country_code">
                <el-select v-model="supplierForm.country_code">
                  <el-option
                    v-for="(item,index) in countryCodeList"
                    :key="index"
                    :label="item.countrycode+' '+item.countryname"
                    :value="item.countrycode">
                  </el-option>
                </el-select>
              </el-form-item>
              <el-form-item class="phone-num" prop="phone_number">
                <el-input v-model.trim="supplierForm.phone_number"></el-input>
              </el-form-item>
            </el-form-item>
            <el-form-item label="登录密码" v-if="supplierForm.status_cn !== 'pendingAudit'">
              <span class="pwd-tip">初始密码为手机号后6位</span>
              <el-button v-if="accountType || supplier.length > 0 && supplier[0]['operator_types'].includes(1)"
                         type="primary"
                         size="mini" @click="resetPwd">重置密码</el-button>
            </el-form-item>
          </div>
        </el-form>
        <div v-if="accountType || supplier.length && supplier[0]['operator_types'].includes(1)"
             class="operate-area">
          <el-button v-if="supplierForm.status_cn === 'pendingAudit'" type="primary" size="small"
                     @click="onSubmit('pass')">审核通过</el-button>
          <el-button v-if="supplierForm.status_cn === 'pendingAudit'" type="info" size="small" class="btn-info"
                     @click="onSubmit('refuse')">审核不通过</el-button>
          <el-button v-else type="primary" size="small" class="btn-primary"
                     @click="onSubmit('update')">更新信息</el-button>
        </div>
      </div>
    </div>
  </div>

</template>
<script>
  import { mapGetters } from 'vuex'
  import { getDetail, getCountryCode, editSupplier } from '@/api/supplier/supplier'
  import rules from './form-rules'
  import uploadList from './upload-list'
  import picturePreview from '../../../components/pages/picture-preview/picture-preview'

  const imgContainer = [
    'business_license',
    'general_taxpayer_certification',
    'corporate_identity_card',
    'permit_of_opening_a_bank_account',
    'nearly_one_year_tax_payment_certificate',
    'trademark_registration_certificate',
    'link_certification',
    'operation_authorization_certificate',
  ]

  export default {
    name: 'supplier-detail',
    components: {
      picturePreview,
    },
    computed: {
      ...mapGetters(['accountType', 'supplier']),
    },
    mounted() {
      // 上传成功事件绑定
      uploadList.forEach((item, index) => {
        uploadList[index].uploaderSetting.success = data => {
          this.supplierForm[imgContainer[index]].push(data)
          this.$message({
            message: '上传成功',
            type: 'success',
          })
        }

        item.uploader.init(item.uploaderSetting)
      })
    },
    data() {
      return {
        supplierForm: {},
        countryCodeList: [],
        tips: '操作成功',
        rules,
      }
    },
    created() {
      this.getSupplierDetail()
      this.getCountryCode()
    },
    methods: {
      handleRemove(data) {
      //        console.log(data)
      },

      getSupplierDetail() {
        const id = this.$route.params.id

        getDetail(id)
          .then(response => {
            const data = response.data.supplier_detail

            // fix: supplierForm内的必要属性可能为空
            imgContainer.forEach(item => {
              if (!data[item]) {
                data[item] = []
              }
            })

            this.supplierForm = {
              ...data,
            }
          })
          .catch(err => {
            // 错误处理
            this.$message.error(err)
          })
      },

      getCountryCode() {
        // 获取国家区号
        getCountryCode().then(res => {
          this.countryCodeList = res.data
        }).catch(err => {
          console.log(err)
        })
      },

      onSubmit(status) {
        this.$refs.supplierForm.validate(valid => {
          if (!valid) return

          if (status === 'refuse') {
            this.supplierForm.status = -1
          } else {
            this.supplierForm.status = 1
          }

          const data = this.supplierForm

          editSupplier(data)
            .then(response => {
              this.$message.info(this.tips)

              setTimeout(() => {
                this.$router.push({
                  path: `/supplier`,
                })
              }, 1500)
            })
        })
      },

      resetPwd() {
        const phone = this.supplierForm.phone_number

        if (phone === '') {
          this.$message.error('填入帐号手机后才可进行重置哟～')

          return
        }

        if (!/^\d{7,13}$/.test(phone)) {
          this.$message.error('请填写正确手机后哟～')

          return
        }

        this.supplierForm.password = phone.substr(-6)
        this.$alert('重置后的密码为账号手机后6位，请点击"更新信息"进行保存', '提示', {
          confirmButtonText: '确定',
        })
      },
    },
  }
</script>
<style scoped lang="scss">
  @import "supplier-detail.scss";
</style>
<style lang="scss">
  .supplier-detail {
    .form-area {
      .el-form-item {
        margin-bottom: 10px;
        &__label {
          padding: 0 0 0 20px;
          text-align: right;
        }
        &__content {
          display: flex;
          padding: 0 0 0 40px;
        }
        &__error {
          padding: 9px 10px;
          position: static;
        }
        .el-input {
          width: 300px;
          height: 30px;
          .el-input__inner {
            height: 30px;
          }
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
            margin:0 6px 0 40px;

            .el-select {
              width: 124px;
              .el-input {
                width: 124px;
              }
              .el-input__suffix {
                top: 1px;
                right: 1px;
                height: 28px;
                background: rgba(209,209,209,1);
                .el-input__icon {
                  width: 28px;
                  height: 28px;
                  color: #fff;
                }
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
    .mt8 {
      margin-top: 8px;
    }
    .operate-area {
      .el-button--small {
        width: 100px;
      }
    }
  }

</style>