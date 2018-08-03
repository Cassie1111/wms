<template>
   <div>
         <h1>路由测试：{{$route.name}}</h1>
   </div>
</template>

<script>
  export default {
    name: 'order',

    data() {
      return {
        loading: false,
        title: 'hello',
        subTitle: 'world',
      }
    },

    created() {
      this.fetchData()
    },

    methods: {
      fetchData() {
        this.loading = true
      },
    },
  }
</script>
