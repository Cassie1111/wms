<template>
  <div>
    <main-header></main-header>
    <div class="page">
      <div class="error-img" :style="{background: `url(${bgImg}) center no-repeat`}"></div>
      <div class="tip">{{ tip }}</div>
    </div>
    <main-footer></main-footer>
  </div>
</template>
<script>
  import MainFooter from '../../components/main-footer/main-footer.vue'
  import MainHeader from '../../components/main-header/main-header.vue'
  import img403 from '../../assets/images/pages/error/error-403.png'
  import img404 from '../../assets/images/pages/error/error-404.png'
  import img500 from '../../assets/images/pages/error/error-500.png'

  export default {
    name: 'error',
    components: {
      MainFooter,
      MainHeader,
    },
    data() {
      return {
        tip: '',
        bgImg: '',
      }
    },
    created() {
      const id = this.$route.params.id

      if (id === '403') {
        this.bgImg = img403
        this.tip = '木有权限哟～'
      } else if (id === '404') {
        this.bgImg = img404
        this.tip = '找不到页面了~'
      } else {
        this.bgImg = img500
        this.tip = '程序好像出错了～'
      }
    },
  }
</script>
<style scoped lang="scss">
  .page {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    min-height: 480px;
    .error-img {
      width: 186px;
      height: 148px;
    }
    .tip {
      padding: 40px 0 0;
      font-size: 16px;
      color: #B5B5B5;
    }
  }
</style>