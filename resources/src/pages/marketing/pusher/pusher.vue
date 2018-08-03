<template>
  <div class="page">
    <div class="location">
      <span>营销中心</span>
      <span>/</span>
      <span>推送消息</span>
    </div>
    <section class="page-content">
      <el-tabs type="card" v-model="activeName" @tab-click="handleClick">
        <el-tab-pane v-if="accountType === 1 || pusher[0]['operator_types'].includes(4)"
                     label="APP推送" name="app">
          <pusherList v-if="activeName === 'app'" :type="1"></pusherList>
        </el-tab-pane>
        <el-tab-pane v-if="accountType === 1 || pusher[0]['operator_types'].includes(5)"
                     label="短信" name="sms">
          <pusherList v-if="activeName === 'sms'" :type="2"></pusherList>
        </el-tab-pane>
      </el-tabs>
    </section>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex'
  import pusherList from './components/pusher.vue'

  export default {
    name: 'pusher',
    components: {
      pusherList,
    },
    data() {
      return {
        activeName: 'app',
      }
    },
    computed: {
      ...mapGetters(['accountType', 'pusher']),
    },
    created() {
      if (this.accountType === 0 && this.pusher[0]['operator_types'].length) {
        const operateType = this.pusher[0]['operator_types']

        // 只有短信权限
        if (!operateType.includes(4)) {
          this.activeName = 'sms'
        }
      }
    },
    methods: {
      handleClick(tab, event) {
        this.activeName = tab.name
      },

    },
  }
</script>
<style scoped lang="scss">
  .page-content {
    padding: 30px 30px 0 40px;
    min-height: 940px;
    background-color: #fff;
  }
</style>
<style lang="scss">
  .page-content {
    .el-tabs--card > .el-tabs__header {
      border-bottom: 2px solid #cdae63;
      .el-tabs__nav {
        display: flex;
        border: none;
        &-wrap {
          margin-bottom: -2px;
        }
      }
      .el-tabs__item {
        width: 100px;
        text-align: center;
        padding: 0;
      }
      .el-tabs__item.is-active {
        border: 2px solid #cdae63;
        border-bottom-color: #fff;
      }
    }
  }
</style>