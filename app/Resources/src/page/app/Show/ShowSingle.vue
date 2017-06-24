<template>
    <div>
        <show-item :show="show">
            <comment-item v-if="comments"  v-for="comm in comments" :comment="comm" :key="comm.getToken()" :respondCallback="respondCallback" :editCallback="editCallback"></comment-item>
        </show-item>
    </div>
</template>

<script>
  /* @flow */
  import Show from '../../../entity/show'
  import CommentItem from '../../../components/CommentItem.vue'
  import ShowItem from '../../../components/ShowItem.vue'
  import CommentService from '../../../service/commentService'
  import ShowService from '../../../service/showService'
  import Envelope from '../../../entity/envelope'
  import Comment from '../../../entity/comment'

  export default{
    data () : { show: Show, comments: [Comment] } {
      return {
        show: null,
        comments: []
      }
    },
    methods: {
      query () {
        const _this = this
        ShowService.getShow(this.$route.params.token, this.$route.params.slug, (data) => {
          _this.$set(_this, 'show', data.getResult())
        }, (data) => {
        })
        ShowService.getShowComments(this.$route.params.token, this.$route.params.slug, null, (data) => {
          _this.$set(_this, 'comments', data.getResult())
        }, (data) => {
        })
      },
      editCallback (current: Comment, response: string, successcallback: (e: Envelope<Comment>) => void, failCallback: (e: Envelope) => void) {
        CommentService.patchComment(current, response, (envelope) => {
          successcallback(envelope)
        }, (envelope) => {
          failCallback(envelope)
        })
      },
      respondCallback (parent: Comment, response: string, successcallback: (e: Envelope<Comment>) => void, failCallback: (e: Envelope) => void) {
        ShowService.postPostComment(this.show, response, parent, (envelope) => {
          successcallback(envelope)
        }, (envelope) => {
          failCallback(envelope)
        })
      }
    },
    created () {
      this.query()
    },
    components: {
      ShowItem,
      CommentItem
    }
  }
</script>