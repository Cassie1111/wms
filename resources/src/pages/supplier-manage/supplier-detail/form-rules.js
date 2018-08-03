
export default {
  supplier_name: [
    {
      required: true,
      message: '请填写供应商名称',
      trigger: 'blur',
    },
  ],
  business_license_no: [
    {
      required: true,
      message: '请填写营业执照编号',
      trigger: 'blur',
    },
  ],
  business_license: [
    {
      required: true,
      message: '请上传营业执照',
      trigger: 'blur',
    },
  ],
  corporate_identity_card: [
    {
      required: true,
      message: '请上传法人身份证(正反面)',
      trigger: 'blur',
    },
  ],
  nearly_one_year_tax_payment_certificate: [
    {
      required: true,
      message: '请上传近一年纳税证明',
      trigger: 'blur',
    },
  ],
  operation_authorization_certificate: [
    {
      required: true,
      message: '请上传运营授权证明',
      trigger: 'blur',
    },
  ],
  opening_bank_name: [
    {
      required: true,
      message: '请填写开户行名称',
      trigger: 'blur',
    },
  ],
  subbranch_name: [
    {
      required: true,
      message: '请填写支行名称',
      trigger: 'blur',
    },
  ],
  bank_account: [
    {
      required: true,
      message: '请填写银行账户',
      trigger: 'blur',
    },
  ],
  contact_name: [
    {
      required: true,
      message: '请填写联系人姓名',
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
      validator(rule, value, callback) {
        const reg = /^\d{5,13}$/
        const arr = value.split('')
        const arrCn = arr.filter(item => item.charCodeAt() !== 8236 && item.charCodeAt() !== 8237)
        const str = arrCn.join('')

        if (!reg.test(str)) {
          callback(new Error('请填写正确手机号'))
        } else {
          callback()
        }
      },
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
  warehouse_location: [
    {
      required: true,
      message: '请填入仓库所在地',
      trigger: 'blur',
    },
  ],
  warehouse_detailed_address: [
    {
      required: true,
      message: '请填入仓库详细地址',
      trigger: 'blur',
    },
  ],
  account_name: [
    {
      required: true,
      message: '请填入账号名称',
      trigger: 'blur',
    },
    {
      pattern: /^[\u4e00-\u9fa5a-zA-Z0-9:]+$/,
      message: '账号名称中不允许使用特殊字符',
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
  phone_number: [
    {
      required: true,
      message: '请填入绑定手机号',
      trigger: 'blur',
    },
    {
      validator(rule, value, callback) {
        const reg = /^\d{5,13}$/
        const arr = value.split('')
        const arrCn = arr.filter(item => item.charCodeAt() !== 8236 && item.charCodeAt() !== 8237)
        const str = arrCn.join('')

        if (!reg.test(str)) {
          callback(new Error('请填写正确手机号'))
        } else {
          callback()
        }
      },
      trigger: 'blur',
    },
  ],
  warehouse_contact_name: [
    {
      required: true,
      message: '请填入仓库联系人',
      trigger: 'blur',
    },
  ],
  warehouse_contact_phone_number: [
    {
      required: true,
      message: '请填入仓库联系人电话',
      trigger: 'blur',
    },
    {
      validator(rule, value, callback) {
        const reg = /^\d{5,13}$/
        const arr = value.split('')
        const arrCn = arr.filter(item => item.charCodeAt() !== 8236 && item.charCodeAt() !== 8237)
        const str = arrCn.join('')

        if (!reg.test(str)) {
          callback(new Error('请填写正确仓库联系人电话'))
        } else {
          callback()
        }
      },
      trigger: 'blur',
    },
  ],
}
