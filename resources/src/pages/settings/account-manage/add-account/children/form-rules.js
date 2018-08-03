const rules = {
  username: [
    {
      required: true,
      message: '请输入账号名称',
      trigger: 'blur',
    },
    {
      validator(rule, value, callback) {
        const pattern = new RegExp('^[\u4e00-\u9fa5a-zA-Z0-9]+$')

        if (!pattern.test(value.trim())) {
          callback(new Error('账号名称中不允许使用特殊字符'))
        } else {
          callback()
        }
      },

      trigger: 'blur',
    },
  ],
  password: [
    {
      pattern: /^[\w-!@#$%^&*?.]{6,18}$/,
      message: '密码应由 6 到 18 个英文字母，数字或符号组成',
      trigger: 'blur',
    },
  ],
  conPassword: [
    {
      pattern: /^[\w-!@#$%^&*?.]{6,18}$/,
      message: '密码应由 6 到 18 个英文字母，数字或符号组成',
      trigger: 'blur',
    },
  ],
  realName: [
    {
      required: true,
      message: '请输入姓名',
      trigger: 'blur',
    },
  ],
  department: [
    {
      required: true,
      message: '请输入部门',
      trigger: 'change',
    },
  ],
  duty: [
    {
      required: true,
      message: '请输入职务',
      trigger: 'change',
    },
  ],
  mobile: [
    {
      pattern: /^\d{5,13}$/,
      message: '请输入正确手机号',
      trigger: 'blur',
    },
  ],
  email: [
    {
      type: 'email',
      message: '请输入正确邮箱格式',
      trigger: 'blur',
    },
  ],
  identity: [
    {
      pattern: /^\d+$/,
      message: '请输入合法身份证',
      trigger: 'blur',
    },
  ],
}

export default rules
