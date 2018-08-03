<template>
  <div class="page">
    <p class="title">营销中心 > 活动管理</p>

    <div class="page-main">
      <p>优惠促销</p>
      <div class="img-box">
        <router-link :to="{name: 'activity-list'}">
          <img src="../../../../assets/images/pages/marketing/seconds-kill.png"/>
        </router-link>
        <div class="activity-name">秒杀活动</div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'activity-manager',
    data() {
      return {}
    },
  }
</script>

<style lang="scss" scoped>
  .title {
    line-height: 40px;
    padding-left: 20px;
    background-color: #ffffff;
    margin-bottom: 10px;
  }

  .page-main {
    width: 100%;
    height: 1158px;
    background-color: #ffffff;
  }

  .page-main p {
    padding: 20px 0 0 30px;
  }

  .img-box {
    width: 135px;
    height: 75px;
    margin: 30px 0 0 80px;
  }

  .img-box img {
    width: 100%;
    height: 100%;
  }

  .activity-name {
    padding-top: 10px;
    padding-left: 35px;
  }
</style>