<template>
    <ul class="chat" v-if="message">
        <li class="left clearfix">
                    <span class="chat-img pull-left">
                        <img src="http://placehold.it/50/55C1E7/fff" alt="User Avatar" class="img-circle">
                    </span>
            <div class="chat-body clearfix">
                <div class="header">
                    <strong class="primary-font" v-if="message.user">
                        {{message.user.username}}
                    </strong>
                    <small class="pull-right text-muted">
                        <i class="fa fa-clock-o fa-fw"></i>
                        {{ sincePost }}
                    </small>
                </div>
                <p>
                    {{message.message}}
                </p>
            </div>
        </li>
    </ul>
</template>

<script>
    import Message from './../chatter/packets/message'
    import Moment from 'moment'
    export default{
      data () {
        return {
          interval: null,
          sincePost: 'now'
        }
      },
      props: {
        message: {
          type: Message,
          default: null
        }
      },
      methods: {
        updatePostTime () {
          if (this.message === null) {
            return 'few seconds ago'
          }
          this.$set(this, 'sincePost', Moment(this.message.data).fromNow())
        }
      },
      mounted () {
        this.$set(this, 'interval', setInterval(this.updatePostTime, 1000))
      },
      beforeDestroy () {
        clearInterval(this.interval)
      }
    }
</script>