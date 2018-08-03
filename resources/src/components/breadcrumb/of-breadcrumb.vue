<template>
  <el-breadcrumb class="breadcrumb">
    <el-breadcrumb-item v-for="item in items">{{ item.title }}</el-breadcrumb-item>
  </el-breadcrumb>
</template>

<script>
  export default {
    name: 'OfBreadcrumb',
    props: {
      items: Array,
    },
  }
</script>

<style scoped>

  .breadcrumb{
    padding: 0 0 0 20px;
    width: 100%;
    height: 40px;
    line-height: 40px;
    background: #fff;
    color: #222;
    font-size: 14px;
  }

</style>