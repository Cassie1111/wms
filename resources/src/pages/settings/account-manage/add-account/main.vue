<template>
  <transition name="fade" mode="out-in">
    <router-view name="login"></router-view>
    <router-view :key="$route.path"></router-view>
  </transition>
</template>