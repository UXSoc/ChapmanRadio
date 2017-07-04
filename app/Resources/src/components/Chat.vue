<template>
    <div class="chat-panel panel panel-default" :class="panel">
        <div class="panel-heading">
            <i class="fa fa-comments fa-fw"></i>
            Chat
        </div>
        <div class="panel-body">
            <chat-message></chat-message>
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
    import ChatMessage from './ChatMessage.vue'
    export default{
      data () {
        return {
          socket: null,
          message: ''
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
          this.socket.send(message)
        }
      },
      watch: {
      },
      created () {
        this.socket = new WebSocket(Config.SocketServer + '/chat')
        const _socket = this.socket
        this.socket.onopen = function () {
          _socket.send('Ping') // Send the message 'Ping' to the server
        }
        this.socket.onerror = function (error) {
          console.log('WebSocket Error ' + error)
        }
        this.socket.onmessage = function (e) {
          console.log('Server: ' + e.data)
        }
      },
      components: {
        ChatMessage
      }
    }
</script>