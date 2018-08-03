<template>
  <div class="container" id="app">
    <div v-if="normalPage">
      <main-header></main-header>
      <div class="content">
        <left-menu @leftMenuChange="changeLeftMenu"></left-menu>
        <router-view  v-if="isRouterAlive" :key="$route.path"></router-view>
      </div>

      <main-footer></main-footer>
    </div>
    <div v-else>
      <router-view name="login"></router-view>
      <router-view name="service"></router-view>
      <router-view name="newLink"></router-view>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import MainHeader from '@/components/main-header/main-header'
  import LeftMenu from '@/components/left-menu/left-menu'
  import MainFooter from '@/components/main-footer/main-footer'

  export default {
    name: 'app',

    data () {
      return {
        isRouterAlive: true,
        isLeftMenuChange: false,
      }
    },

    watch:{
      $route () {
        if (this.isLeftMenuChange) {
          this.isLeftMenuChange = false
          this.isRouterAlive = false
          this.$nextTick(() => {
            this.isRouterAlive = true
          })
        }
      }
    },

    components: {
      LeftMenu,
      MainFooter,
      MainHeader,
    },

    created() {
      window.onstorage = () => {
        window.location.reload()
      }
    },

    computed: mapState({
      normalPage: state => state.view.normalPage,
    }),

    methods: {
      changeLeftMenu () {
        this.isLeftMenuChange = true
      },
    },
  }
</script>
