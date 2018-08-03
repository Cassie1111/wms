<template>
  <div class="new-link-page">
    <div class="page-header">
      <div class="logo">
        <img src="../../../assets/images/common/main-header/logo.png">
      </div>
      <div class="header-info">
        <span>欢迎来到OFashion</span>
      </div>
    </div>
    <div class="page-body">
      <p>点击下方按钮生成新的注册链接</p>
      <el-button class="new-link-btn" type="primary" size="medium" @click="getNewLink">生成</el-button>
      <div  v-if="link !== ''">
        <input class="link-input" type="text" v-model="link">
        <el-button class="copy-btn" type="primary" size="mini" v-clipboard:copyhttplist="link" v-clipboard:success="onCopy">复制链接</el-button>
      </div>
    </div>
    <main-footer></main-footer>
  </div>
</template>

<script>
  import Vue from 'vue'
  import VueClipboard from 'vue-clipboard2'
  import { Message } from 'element-ui'
  import MainHeader from '@/components/main-header/main-header'
  import MainFooter from '@/components/main-footer/main-footer'
  import { getSignature } from '@/api/auth/new-link'

  Vue.use(VueClipboard)

  export default {
    name: 'newLink',
    components: {
      MainHeader,
      MainFooter,
    },

    data() {
      return {
        link: '',
      }
    },

    methods: {
      getNewLink() {
        getSignature().then(res => {
          Message({
            message: '新的链接已生成',
            type: 'success',
          })

          const sign = res.data.sign
          const timeStamp = res.data.t
          const key = res.data.key

          switch (process.env.NODE_ENV) {
            case 'development':
              this.link = `http://local.ofashion.com.cn:9090/register?registerSign=${sign}&t=${timeStamp}&key=${key}`
              break
            case 'test':
              this.link = `https://tsupplier.ofashion.com.cn/register?registerSign=${sign}&t=${timeStamp}&key=${key}`
              break
            default:
              this.link = `https://supplier.ofashion.com.cn/register?registerSign=${sign}&t=${timeStamp}&key=${key}`
          }
        })
      },

      onCopy() {
        Message({
          message: '链接已复制',
          type: 'success',
        })
      },
    },
  }
</script>

<style lang="scss" scoped>
  $theme-color: #cdae63;

  @mixin btn{
    border: 0;
    font-size: 16px;
    color: #fff;
  }

  .new-link-page {
    width: 1200px;
    margin: 0 auto;

    .page-header {
      display: -webkit-flex;
      display: flex;
      height: 63px;
      padding: 0 28px;
      justify-content: space-between;
      align-items: center;
      background: $theme-color;

      .logo {
        width: 200px;
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
      }
    }

    .page-body {
      height: 800px;
      padding: 100px;
      text-align: center;
      background: #fff;

      p {
        font-size: 18px;
        color: #666;
      }

      .new-link-btn {
        width: 120px;
        margin: 40px 0 80px;
        @include btn;
      }

      .link-input {
        width: 500px;
        height: 30px;
      }

      .copy-btn {
        width: 100px;
        height: 30px;
        font-size: 14px;
        color: #fff;
      }
    }
  }
</style>