<template>
    <div class="chat-panel panel panel-default" :class="panel">
        <div class="panel-heading">
            <i class="fa fa-comments fa-fw"></i>
            Chat
        </div>
        <div class="panel-body">
            <chat-message v-for="message in messages" :key="message.timestamp" :message="message"></chat-message>
            <!--<ul class="chat">-->
                <!--<li class="left clearfix">-->
                    <!--<span class="chat-img pull-left">-->
                        <!--<img src="http://placehold.it/50/55C1E7/fff" alt="User Avatar" class="img-circle">-->
                    <!--</span>-->
                    <!--<div class="chat-body clearfix">-->
                        <!--<div class="header">-->
                            <!--<strong class="primary-font">-->
                                <!--Jack Sparrow-->
                            <!--</strong>-->
                            <!--<small class="pull-right text-muted">-->
                                <!--<i class="fa fa-clock-o fa-fw"></i>-->
                                <!--12 mins ago-->
                            <!--</small>-->
                        <!--</div>-->
                        <!--<p>-->
                            <!--Lorem ipsum dolor sin amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.-->
                        <!--</p>-->
                    <!--</div>-->
                <!--</li>-->
            <!--</ul>-->
            <!--<ul class="chat">-->
                <!--<li class="right clearfix">-->
                    <!--<span class="chat-img pull-right">-->
                        <!--<img src="http://placehold.it/50/FA6F57/fff" alt="User Avatar" class="img-circle">-->
                    <!--</span>-->
                    <!--<div class="chat-body clearfix">-->
                        <!--<div class="header">-->
                            <!--<small class="text-muted">-->
                                <!--<i class="fa fa-clock-o fa-fw"></i>-->
                                <!--13 mins ago-->
                            <!--</small>-->
                            <!--<strong class="pull-right primary-font">-->
                                <!--Will Turner-->
                            <!--</strong>-->
                        <!--</div>-->
                        <!--<p>-->
                            <!--Lorem ipsum dolor sin amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.-->
                        <!--</p>-->
                    <!--</div>-->
                <!--</li>-->
            <!--</ul>-->
            <!--<ul class="chat">-->
                <!--<li class="left clearfix">-->
                                        <!--<span class="chat-img pull-left">-->
                                            <!--<img src="http://placehold.it/50/55C1E7/fff" alt="User Avatar" class="img-circle">-->
                                        <!--</span>-->
                    <!--<div class="chat-body clearfix">-->
                        <!--<div class="header">-->
                            <!--<strong class="primary-font">-->
                                <!--Jack Sparrow-->
                            <!--</strong>-->
                            <!--<small class="pull-right text-muted">-->
                                <!--<i class="fa fa-clock-o fa-fw"></i>-->
                                <!--12 mins ago-->
                            <!--</small>-->
                        <!--</div>-->
                        <!--<p>-->
                            <!--Lorem ipsum dolor sin amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.-->
                        <!--</p>-->
                    <!--</div>-->
                <!--</li>-->
            <!--</ul>-->
            <!--<ul class="chat">-->
                <!--<li class="right clearfix">-->
                    <!--<span class="chat-img pull-right">-->
                        <!--<img src="http://placehold.it/50/FA6F57/fff" alt="User Avatar" class="img-circle">-->
                    <!--</span>-->
                    <!--<div class="chat-body clearfix">-->
                        <!--<div class="header">-->
                            <!--<small class="text-muted">-->
                                <!--<i class="fa fa-clock-o fa-fw"></i>-->
                                <!--13 mins ago-->
                            <!--</small>-->
                            <!--<strong class="pull-right primary-font">-->
                                <!--Will Turner-->
                            <!--</strong>-->
                        <!--</div>-->
                        <!--<p>-->
                            <!--Lorem ipsum dolor sin amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.-->
                        <!--</p>-->
                    <!--</div>-->
                <!--</li>-->
            <!--</ul>-->
        </div>
        <div class="panel-footer">
            <div class="input-group">
                <input id="btn-input" type="text" v-model="message" class="form-control input-sm" placeholder="Type your message here...">
                <span class="input-group-btn">
                    <button class="btn btn-warning btn-sm" id="btn-chat" v-on:click.prevent="sendMessage(message)">
                        Send
                    </button>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    /* @flow */
    import Config from './../config'
    import ChatService from './../service/chatService'
    import ChatMessage from './ChatMessage.vue'
    import Chatter from '../chatter/chatter'
    import Message from '../chatter/packets/message'
    export default {
      data () {
        return {
          chatter: null,
          message: '',
          messages: []
        }
      },
      props: {
        panel: {
          type: String,
          default: ''
        }
      },
      methods: {
        sendMessage: function (message: string) {
          this.chatter.sendMessage(message)
        }
      },
      watch: {
      },
      created () {
        const _this = this
        const chatter = new Chatter(Config.SocketServer + '/chat')
        _this.$set(_this, 'chatter', chatter)
        ChatService.getChatToken((token) => {
          _this.chatter.authenticate(token)
        })
        _this.chatter.setMessageCallback((response) => {
          if (response instanceof Message) {
            if (_this.messages.length > 100) {
              _this.messages.shift()
            }
            _this.messages.push(response)
          }
        }, (e) => {})
      },
      components: {
        ChatMessage
      }
    }
</script>