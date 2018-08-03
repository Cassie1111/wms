<template>
  <div class="left-menu">
    <div class="menu-list" v-if="accountType || home.length > 0">
      <div class="main-menu">
        <span class="menu-icon">
          <img src="../../assets/images/common/left-menu/menu1-icon.png">
        </span>
        <span class="menu-text">
          <router-link to="/" @click.native="flushPage('/')" exact>我的</router-link>
        </span>
      </div>
    </div>

    <div class="menu-list" v-if="accountType || supplier.length > 0">
      <div class="main-menu">
        <span class="menu-icon">
          <img src="../../assets/images/common/left-menu/menu2-icon.png">
        </span>
        <span class="menu-text">
          供应商管理
        </span>
      </div>
      <div class="sub-menu">
        <router-link :to="{name: 'supplier-list'}" @click.native="flushPage('/supplier')">
          我的供应商
        </router-link>
      </div>
    </div>

    <div class="menu-list" v-if="accountType || retailer.length > 0">
      <div class="main-menu">
        <span class="menu-icon">
          <img src="../../assets/images/common/left-menu/menu3-icon.png">
        </span>
        <span class="menu-text">
          分销商管理
        </span>
      </div>
      <div class="sub-menu">
        <router-link :to="{name: 'retailer-list'}" @click.native="flushPage('/retailer')">
          我的分销商
        </router-link>
      </div>
    </div>

    <div class="menu-list"
         v-if="accountType || sellingGoods.length > 0 || warehouse.length > 0 || createGoods.length > 0">
      <div class="main-menu">
        <span class="menu-icon">
          <img src="../../assets/images/common/left-menu/menu4-icon.png">
        </span>
        <span class="menu-text">货品管理</span>
      </div>
      <div class="sub-menu" v-if="accountType || sellingGoods.length > 0">
        <router-link :to="{name: 'selling-goods'}" @click.native="flushPage('/goods/selling-goods/')">
          分销中的商品
        </router-link>
      </div>
      <div class="sub-menu" v-if="accountType || warehouse.length > 0">
        <router-link :to="{name: 'warehouse'}" @click.native="flushPage('/goods/warehouse/')">
          我的仓库
        </router-link>
      </div>
      <div class="sub-menu" v-if="accountType || createGoods.length > 0">
        <router-link :to="{name: 'create-goods'}" @click.native="flushPage('/goods/create-goods/')">
          创建新商品
        </router-link>
      </div>
    </div>

    <div class="menu-list"
         v-if="accountType || settingOrder.length > 0 || order.length > 0 || refund.length > 0">
      <div class="main-menu">
        <span class="menu-icon">
          <img src="../../assets/images/common/left-menu/menu5-icon.png">
        </span>
        <span class="menu-text">订单管理</span>
      </div>
      <div class="sub-menu" v-if="accountType || settingOrder.length > 0">
        <router-link :to="{name: 'setting-order-list'}" @click.native="flushPage('/order/setting-order-list')">
          我的订单
        </router-link>
      </div>
      <div class="sub-menu" v-if="accountType || order.length > 0">
        <router-link :to="{name: 'order-list'}" @click.native="flushPage('/order/order-list')">
          我的采购单
        </router-link>
      </div>
      <div class="sub-menu" v-if="accountType || refund.length > 0">
        <router-link :to="{name: 'refund'}" @click.native="flushPage('/order/refund')">
          退款管理
        </router-link>
      </div>
    </div>

    <div class="menu-list"
         v-if="accountType
         || banner.length > 0
         || activityConfig.length > 0
         || activityManager.length > 0
         || resource.length > 0
         || pusher.length > 0">
      <div class="main-menu">
        <span class="menu-icon">
          <img src="../../assets/images/common/left-menu/menu6-icon.png">
        </span>
        <span class="menu-text">营销中心</span>
      </div>

      <div class="sub-menu" v-if="accountType || banner.length > 0 || activityManager.length > 0">
        <router-link :to="{name: !accountType && banner.length < 1 ? 'activity-config' : 'banner-list'}" @click.native="flushPage('/banner-list')">
          模版管理
        </router-link>
      </div>
      <div class="sub-menu" v-if="accountType || activityManager.length > 0">
        <router-link :to="{name: 'activity-manager'}" @click.native="flushPage('/activity-manager')">
          活动管理
        </router-link>
      </div>
      <div class="sub-menu" v-if="accountType || resource.length > 0">
        <router-link :to="{name: 'resource-list'}" @click.native="flushPage('/resource-list')">
          资源管理
        </router-link>
      </div>
      <div class="sub-menu" v-if="accountType || pusher.length > 0">
        <router-link :to="{name: 'pusher'}" @click.native="flushPage('/pusher')">
          推送
        </router-link>
      </div>
    </div>

    <div class="menu-list" v-if="accountType || funds.length > 0">
      <div class="main-menu">
        <span class="menu-icon">
          <img src="../../assets/images/common/left-menu/menu7-icon.png">
        </span>
        <span class="menu-text">
          资金管理
        </span>
      </div>
      <div class="sub-menu">
        <router-link :to="{name: 'funds'}" @click.native="flushPage('/funds')">
          结算单管理
        </router-link>
      </div>
    </div>

    <div class="menu-list" v-if="accountType || account.length > 0">
      <div class="main-menu">
        <span class="menu-icon">
          <img src="../../assets/images/common/left-menu/menu8-icon.png">
        </span>
        <span class="menu-text">
          设置
        </span>
      </div>
      <div class="sub-menu">
        <router-link :to="{name: 'account-list'}" @click.native="flushPage('/setting/account-manage')">
          子账号管理
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { setTimeout } from 'timers';

  export default {
    name: 'left-menu',

    computed: {
      ...mapGetters([
        'accountType',
        'home',
        'supplier',
        'retailer',
        'sellingGoods',
        'warehouse',
        'createGoods',
        'settingOrder',
        'order',
        'refund',
        'banner',
        'activityConfig',
        'activityManager',
        'resource',
        'pusher',
        'funds',
        'account',
      ]),
    },

    methods: {
      flushPage(path) {
        this.$emit('leftMenuChange')
        this.$router.push({
          path: path,
          query:{
            t: `${new Date().getTime()}${Math.floor(Math.random()*9999+1000)}`
          }
        })
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import './left-menu.scss';
</style>