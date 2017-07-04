<template>
    <ul class="chat" v-if="chatMessage">
        <li class="left clearfix">
                    <span class="chat-img pull-left">
                        <img src="http://placehold.it/50/55C1E7/fff" alt="User Avatar" class="img-circle">
                    </span>
            <div class="chat-body clearfix">
                <div class="header">
                    <strong class="primary-font">
                        Jack Sparrow
                    </strong>
                    <small class="pull-right text-muted">
                        <i class="fa fa-clock-o fa-fw"></i>
                        12 mins ago
                    </small>
                </div>
                <p>
                    {{chatMessage.message}}
                </p>
            </div>
        </li>
    </ul>
</template>

<script>
    import ChatMessage from './../entity/chatMessage'
    export default{
      data () {
        return {
        }
      },
      props: {
        chatMessage: {
          type: ChatMessage,
          default: null
        }
      }
    }
</script>