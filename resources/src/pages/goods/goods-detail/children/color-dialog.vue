<template>
  <div class="color-picker">
    <ul class="color-picker-left">
      <li v-for="(item,index) in colorInfo" :key="index" @click="selectSupColor(index)" class="sup-color-item">
        <div class="color-item" :style="'background:' + item.color_value"
        v-if="item.code_name !== '花色系'"></div>
        <div v-else class="color-item" :style="'background:url(' + item.color_url+')'"></div>
        {{item.code_name}}
      </li>
    </ul>
    <div class="color-picker-right">
      <p>常用标准颜色，请参考实物颜色酌情选择：</p>
      <div class="sub-color-list">
        <div v-for="(item,index) in colorList" :key="index" @click="selectSubcolor(item)" class="sub-color-item">
          <div class="color-item" :style="'background:' + item.color_value"
          v-if="item.color_value"
          ></div>
          <div v-else class="color-item" :style="'background:url(' + item.color_url+')'"></div>
            {{item.name}}
        </div>
      </div>
      <i class="el-icon-close" @click="closeDialog"></i>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    colorInfo: {
      type: Array,
      'default': [],
    },
  },
  data() {
    return {
      colorList: this.colorInfo[0].data_list,
    }
  },
  methods: {
    closeDialog() {
      this.$emit('close')
    },
    selectSupColor(index) {
      this.colorList = this.colorInfo[index].data_list
    },
    selectSubcolor(item) {
      this.$emit('close', item.name)
    },
  },
}
</script>
<style lang="scss" scoped>
.color-picker {
  display:flex;
	position: absolute;
	z-index: 999;
	box-shadow: 1px 1px 3px #ccc;
	background: #fff;
	width: 600px;
	top: 36px;
  border-radius:2px;
}
.color-picker-left li {
	padding: 10px 10px;
	width: 120px;
	border-right: 1px solid #eee;
	cursor: pointer;
  background:#fff;
}
.color-picker-left li:hover {
	box-shadow: 1px 1px 1px #999;
}
.color-picker-left {
	position: relative;
  margin:0;
  padding:0;
}
.color-picker-left:before {
	content: '';
	position: absolute;
	border: 6px solid #000;
	border-color: transparent transparent #fff transparent;
	top: -12px;
	left: 30px;
}
.color-picker-right {
	padding: 10px;
}
.color-item{
  width:20px;
  height:20px;
  border:1px solid #eee;
  margin-right:5px;
  border-radius: 2px;
}
.el-icon-close {
	position: absolute;
	right: 4px;
	top: 4px;
	font-size: 18px;
	cursor: pointer;
}
.sub-color-item,.sup-color-item{
  display:flex;
  width:80px;
  align-items:center;
  margin-right:20px;
  margin-bottom:10px;
  cursor: pointer;
  transition:all .25s;
  &:hover {
    transform:scale(1.04);
  }
}
.sup-color-item{
  margin-bottom:0;
}
.sub-color-list{
  display:flex;
  flex-wrap:wrap;
}
p{
  margin-bottom:20px;
}
</style>
