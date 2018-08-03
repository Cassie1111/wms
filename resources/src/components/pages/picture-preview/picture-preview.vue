<template>
  <div class="picture-preview-container">
    <!--预览图片列表-->
    <div class="small-picture-list">
      <div class="item"
           v-show="pictureListIsShow"
           :style="{'width':`${width}px`, 'height':`${height}px`}"
           v-for="(item, index) in pictureList"
           @mouseover="showIconIndex = index"
           @mouseout="showIconIndex = -1"
           @click="showBigPicturePreview(index)">
        <div class="picture" :style="{background: `url(${item.path_full}) no-repeat center center / cover`}"></div>
        <div v-if="isDelete" class="delete-img el-icon-circle-close" :data-index="index" @click="deleteImg"></div>
      </div>
    </div>
    <!--放大层-->
    <div>
      <div class="big-picture-preview" v-if="ifShowPicturePreview">
        <div class="picture-wrapper" ref="imgWrapper" @click="singleClick">
          <img
            :class="{show: isShowPic}"
            :src="currentPicSrc"
            alt=""
            :style="{transition: `${imgTransition}s`,transform: `scale(${scaleVal})`, left: `${left}px`, top: `${top}px`}"
            ref="img"
            @load="imageLoaded"
            @mousedown="touchStart"
            @mousemove="touchMove"
            @mouseup="touchEnd"
            @mousewheel="mousewheel"
            @click="doubleClick"
          >
          <div class="loading el-icon-loading" v-show="!isShowPic"></div>
        </div>
        <div class="menu-wrapper">
          <div class="item el-icon-arrow-left" :class="{disabled: isFirst}" @click="moveIndex(-1)"></div>
          <div class="item el-icon-zoom-in" @click="ScaleImg(1)"></div>
          <div class="item el-icon-zoom-out" @click="ScaleImg(-1)"></div>
          <div class="item el-icon-arrow-right" :class="{disabled: isLast}" @click="moveIndex(1)"></div>
          <div class="close-icon el-icon-close" @click="closeBigPicturePreview"><i></i></div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    props: {
      // 图片
      pictureList: {
        type: Array,
        'default': () => [],
      },

      // 数据配置
      props: {
        type: Object,
        'default': () => ({}),
      },
      pictureListIsShow: {
        'default': true,
      },
      width: {
        'default': 30,
      },
      height: {
        'default': 30,
      },
      isDelete: {
        'default': true,
      },

      // 是否单机黑框关闭弹出层
      isSingleClickToClose: {
        'default': true,
      },

      // 鼠标放大缩小
      isMousewheelScale: {
        'default': true,
      },

      // 双击恢复
      doubleRestore: {
        'default': true,
      },
    },
    data() {
      return {
        // 图片加载完成之后再显示
        isShowPic: false,
        startMove: false,
        left: 0,
        top: 0,
        imgWidth: 0,
        imgHeight: 0,

        // 触摸位置X、Y
        startX: 0,
        startY: 0,

        // 移动距离X、Y
        disX: 0,
        disY: 0,

        // 上一次移动距离X、Y
        lastDisX: 0,
        lastDisY: 0,

        // 节流计时器
        time: 1,

        // 双击次数（用于单位事件判断是否双击)
        clickTime: 0,

        // 用于判断是点击还是拖动时候的第一次单击
        removeClose: true,
        showIconIndex: -1,
        currentPicSrc: '',
        currentPicIndex: -1,
        isFirst: false,
        isLast: false,
        scaleVal: 1,
        ifShowPicturePreview: false,
        imgTransition: 0.3,
      }
    },
    watch: {
      // 监听当前大图的url变化，变化了就先隐藏图片待图片加载完成之后再显示
      currentPicSrc() {
        this.isShowPic = false
      },
    },
    methods: {
      deleteImg(e) {
        e.stopPropagation()
        const index = e.target.dataset.index

        this.pictureList.splice(index, 1)

        //      this.$emit('onRemove', this.pictureList)
      },

      // 初始换图片状态
      imageLoaded() {
        this.isShowPic = true

        // 在显示图片之后, 设置一个延迟时间重置图片位置，否则图片位置错乱
        setTimeout(() => {
          this.scaleVal = 1
          this.imgWidth = this.$refs.img.width
          this.imgHeight = this.$refs.img.height
          const imgWrapperWidth = this.$refs.imgWrapper.offsetWidth
          const imgWrapperHeight = this.$refs.imgWrapper.offsetHeight

          this.left = (imgWrapperWidth - this.imgWidth) / 2
          this.top = (imgWrapperHeight - this.imgHeight) / 2
        }, 50)
      },

      // 判断是否最后一页和第一页
      chargeFirstOrLast(l) {
        if ((this.currentPicIndex === 0 || this.currentPicIndex === l - 1) && l === 1) {
          this.isFirst = true
          this.isLast = true
        } else if (this.currentPicIndex === 0) {
          this.isFirst = true
          this.isLast = false
        } else if (this.currentPicIndex === l - 1) {
          this.isFirst = false
          this.isLast = true
        } else {
          this.isFirst = false
          this.isLast = false
        }
      },

      // 打开大图
      showBigPicturePreview(index) {
        this.currentPicSrc = this.pictureList[index]['path_full']
        this.currentPicIndex = index
        this.ifShowPicturePreview = true
        const l = this.pictureList.length

        this.chargeFirstOrLast(l)
      },

      // 关闭大图
      closeBigPicturePreview() {
        this.ifShowPicturePreview = false
        this.currentPicSrc = ''
        this.currentPicIndex = -1
      },

      // 前进后退
      moveIndex(way) {
        const l = this.pictureList.length

        way === -1 ? this.currentPicIndex -= 1 : this.currentPicIndex += 1
        if (this.currentPicIndex <= 0) {
          this.currentPicIndex = 0
        } else if (this.currentPicIndex >= l - 1) {
          this.currentPicIndex = l - 1
        }
        this.chargeFirstOrLast(l)
        this.currentPicSrc = this.pictureList[this.currentPicIndex]['path_full']
        const params = {
          direction: way,
          src: this.currentPicSrc,
          index: this.currentPicIndex,
          isFirst: this.isFirst,
          isLast: this.isLast,
        }

        this.$emit('move', params)
      },

      // 缩放
      ScaleImg(way, scale = 0.1) {
        const oldVal = this.scaleVal

        if (way === -1) {
          if (this.scaleVal <= 0.5) {
            this.scaleVal = oldVal
          } else {
            this.scaleVal -= scale
          }
        } else if (this.scaleVal >= 2) {
          this.scaleVal = oldVal
        } else {
          this.scaleVal += scale
        }
      },

      // 触摸开始
      touchStart(e = window.event) {
        e.preventDefault()
        this.imgTransition = 0
        this.removeClose = true
        this.startX = e.clientX
        this.startY = e.clientY
        this.startMove = true
      },

      // 滑动过程
      touchMove(e = window.event) {
        if (this.startMove) {
          this.disX = e.clientX - this.startX
          this.disY = e.clientY - this.startY
          this.left = this.left - this.lastDisX + this.disX
          this.top = this.top - this.lastDisY + this.disY
          this.lastDisX = this.disX
          this.lastDisY = this.disY
          this.removeClose = false
        }
      },

      // 滑动结束
      touchEnd() {
        this.imgTransition = 0.3
        this.startMove = false
        this.lastDisX = 0
        this.lastDisY = 0
      },

      // 鼠标滚动
      mousewheel(e = window.event) {
        if (!this.isMousewheelScale) return
        e.preventDefault()
        if (e.deltaY > 0) {
          this.throttle(() => {
            this.ScaleImg(-1, 0.02)
          })
        } else {
          this.throttle(() => {
            this.ScaleImg(1, 0.02)
          })
        }
      },

      // 双击重置图片状态
      doubleClick(e = window.event) {
        e.stopPropagation()
        this.clickTime += 1
        setTimeout(() => {
          // 如果是双击重置图片
          if (this.clickTime === 2) {
            this.removeClose = false
            if (this.doubleRestore) this.imageLoaded()
          }
          this.clickTime = 0
        }, 300)
      },

      // 单层外层关闭图片
      singleClick(e = window.event) {
        e.stopPropagation()
        if (this.isSingleClickToClose) {
          this.closeBigPicturePreview()
        }
      },

      // 节流
      throttle(func) {
        if (this.time) {
          this.time = 0
          func()
          setTimeout(() => {
            this.time = 1
          }, 20)
        }
      },
    },
  }
</script>
<style scoped lang="scss">
  .picture-preview-container {
    .small-picture-list {
      display: flex;
      flex-wrap: wrap;
      .item {
        margin: 5px;
        position: relative;
        background-color: #ccc;
        border-radius: 5px;
        transition: .1s;
        .picture {
          width: 100%;
          height: 100%;
        }
        .delete-img {
          position: absolute;
          top: -5px;
          right: -5px;
          transform: scale(0);
        }
        &:hover {
          cursor: pointer;
          .delete-img {
            transform: scale(1);
          }
        }
      }
    }
    .big-picture-preview{
      position: fixed;
      z-index: 9999999999;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      background: rgba(0,0,0,0.9);
      .picture-wrapper{
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 60px;
        img {
          max-width: 100%;
          max-height: 100%;
          position: absolute;
          left: 0;
          top: 0;
          cursor: move;
          opacity: 0;
          &.show{
            opacity: 1;
          }
        }
        .loading{
          width: 32px;
          height: 32px;
          position: absolute;
          left: 50%;
          top: 50%;
          margin-left: -16px;
          margin-top: -16px;
          -webkit-animation:scale 2s infinite linear ;
        }
        @-webkit-keyframes scale {
          0%{
            -webkit-transform: rotate(0deg);
          }
          50%{
            -webkit-transform: rotate(180deg);
          }
          100%{
            -webkit-transform: rotate(360deg);
          }
        }
      }
      .menu-wrapper{
        position: absolute;
        height: 60px;
        left: 0;
        right: 0;
        bottom: 0;
        background: #f2f2f2;
        display: flex;
        justify-content: center;
        align-items: center;
        .item {
          // padding: 20px 30px;
          margin: 0 30px;
          transform: scale(2);
          &.disabled{
            opacity: 0.2;
          }
          &:hover {
            cursor: pointer;
          }
        }
        .close-icon {
          position: absolute;
          right: 10px;
          // top: 10px;
          transform: scale(2);
          cursor: pointer;
        }
      }
    }
  }
  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
  }
</style>