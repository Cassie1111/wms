<template>
  <div>
    <ul class="message-list" v-if="list.length > 0">
      <li class="list-item" v-for="(item, index) in list">
        <router-link class="item-router" :to=item.ulink v-if="type == 'message'"></router-link>
        <router-link class="item-router" :to="'/announcement/detial/' + item.id " v-if="type == 'announcement'"></router-link>
        <p class="item-title">{{`${item.message_type?'【': ''}${item.title}${item.message_type?']': ''}`}}</p>
        <div class="item-content" v-html="item.content"></div>
        <span class="item-time">{{item.create_time}}</span>
      </li>
    </ul>
    <div v-else-if="requierFlag" class="message-empty">
      <div class="empty-img"></div>
      <p class="empty-word">暂无数据</p>
    </div>
  </div>
</template>

<script>
  export default {
    props: [
      'list',
      'requierFlag',
      'type',
    ],
  }
</script>

<style lang="scss" scoped>
  $font-color: #666;
  $fonts-normal: 14px;
  .container-message {
    position: relative;
  }
  .message-list {
    padding-bottom: 1px;
    font-size: $fonts-normal;
    color: $font-color;
    .list-item {
      position: relative;
      width: 880px;
      height: 84px;
      margin-bottom: 20px;
      padding: 16px 20px;
      list-style: none;
      background-color: #f6f6f6;
      .item-router {
        position: absolute;
        top: 0;
        left: 0;
        display: inline-block;
        width: 100%;
        height: 100%;
      }
      .item-title {
        margin-bottom: 14px;
      }
      .item-content {
        height: 21px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        p {
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
        }
      }
      .item-time {
        position: absolute;
        top: 16px;
        right: 20px;
        display: inline-block;
        width: 160px;
        height: 26px;
        text-align: right;
      }
    }
    .list-item:last-child {
      margin-bottom: 24px;
    }
  }
  .message-empty {
    width: 96px;
    margin: auto;
    margin-top: 150px;
    text-align: center;
    .empty-img {
      width: 96px;
      height: 94px;
      background: url('../../assets/images/pages/message/empty.png');
      background-size: 100%;
    }
    .empty-word {
      margin-top: 30px;
      font-size: $fonts-normal;
      color: #d1d1d1;
    }
  }
</style>