<template>
  <div class="login-page">
    <div class="login-page-header">
      <div class="logo">
        <img src="../../../assets/images/common/main-header/logo.png">
      </div>
      <div class="header-info">
        <span>欢迎来到迷橙买手宝</span>
      </div>
    </div>
    <div class="content-container">
      <div class="content">
        <div class="login-form">
        <div class="login-form-title">迷橙买手宝</div>
        <div class="login-form-body" v-loading="loading">
          <div class="line-item">
            <img src="../../../assets/images/pages/auth/account-icon.png">
            <input type="text" v-model="username" placeholder="请输入账号">
          </div>
          <div class="line-item">
            <img src="../../../assets/images/pages/auth/pwd-icon.png">
            <input type="password" v-model="password" placeholder="请输入密码">
          </div>
          <el-button class="submit-btn" type="primary" size="medium" @click="submit" :disabled="isDisable">
            登录
          </el-button>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { Message } from 'element-ui'
  import { login } from '@/api/auth/login'

  export default {
    data() {
      return {
        username: '',
        password: '',
        loading: false,
        tips: '',
        isDisable: false,
      }
    },

    methods: {
      submit() {
        this.validForm()

        // 登陆信息不完整
        if (this.tips !== '') {
          this.isDisable = true
          Message({
            message: this.tips,
            type: 'error',
          })

          // 防止用户多次重复点击
          setTimeout(() => {
            this.isDisable = false
          }, 2000)

          return
        }

        this.isDisable = true
        this.loading = true

        login(this.username, this.password)
          .then(res => {
            this.$store.commit('setUserInfo', res.data)
            this.$router.push('/')
          })
          .catch(() => {
            this.loading = false
            this.isDisable = false
          })
      },

      validForm() {
        if (!this.username) {
          this.tips = '请输入账号'

          return
        }

        if (!this.password) {
          this.tips = '请输入密码'

          return
        }

        this.tips = ''
      },
    },
  }
</script>

<style lang="scss" scoped>
  @mixin box-size($width, $height) {
    width: $width;
    height: $height;
  }

  @media screen and (max-width: 1480px) {
    .login-page {
      &-header {
        min-width: 1280px;
      }

      .content-container {
        min-width: 1280px;

        .content {
          height: 960px;

          .login-form {
            @include box-size(460px, 590px);
            margin: 65px 110px 0 0;

            &-title {
              margin: 120px 0 100px 0;
            }

            .submit-btn {
              margin: 80px auto 0;
            }
          }
        }
      }
    }
  }

  @media screen and (min-width: 1481px) {
    .login-page {
      &-header {
        min-width: 1920px;
      }

      .content-container {
        min-width: 1920px;

        .content {
          height: 1015px;

          .login-form {
            @include box-size(560px, 860px);
            margin: 80px 110px 0 0;

            &-title {
              margin: 144px 0 170px 0;
            }

            .submit-btn {
              margin: 60px auto 0;
            }
          }
        }
      }
    }
  }

  .login-page {
    width: 100%;

    &-header {
      height: 63px;
      padding: 0 30px;
      line-height: 63px;
      background: #cdae63;

      .logo {
        float: left;
        width: 100px;
        height: 30px;

        img {
          width: 100%;
          height: 100%;
        }
      }

      .header-info {
        float: right;
        color: #fff;
        font-size: 14px;
        span {
          margin-left: 24px;
        }
      }
    }
    .content-container {
      clear: both;
      width: 100%;
      background: url('../../../assets/images/pages/auth/login-bg.png') no-repeat;
      background-size: cover;

      .content {
        display: block;
        width: 100%;
        margin: 0 auto;

        .login-form {
          float: right;
          background: #fff;
          box-shadow: 2px 2px 40px 0 rgba(0,0,0,0.2);
          text-align: center;

          &-title {
            font-size: 50px;
            line-height: 58px;
            color: #444;
          }

          &-body {
            .line-item {
              position: relative;
              @include box-size(300px, 40px);
              margin: 20px auto;
              padding: 2px 10px;
              border: 1px solid #cacaca;
              border-radius: 2px;

              img {
                position: absolute;
                top: 9px;
                left: 10px;
                @include box-size(18px, 20px);
              }

              input {
                position: absolute;
                top: 0px;
                left: 38px;
                @include box-size(250px, 38px);
                font-size: 14px;
                border: 0px;
                outline: none;
              }
            }
          }

          .submit-btn {
            @include box-size(300px, 40px);
            color: #fff;
            font-size: 20px;
            border: 0;
          }
        }
      }
    }
  }
</style>