<template>
    <div>
        <post-item v-if="post" :post="post">
            <comment-item v-if="comments"  v-for="comm in comments" :comment="comm" :key="comm.getToken()" :respondCallback="respondCallback" :editCallback="editCallback"></comment-item>
        </post-item>
    </div>
</template>

<script>
    import PostService from '../../../service/postService'
    import PostItem from '../../../components/PostItem.vue'
    import Envelope from '../../../entity/envelope'
    import CommentItem from '../../../components/CommentItem.vue'
    import CommentService from '../../../service/commentService'
    import Post from '../../../entity/post'

    export default{
      data () : { post:Post, comments: [Comment] } {
        return {
          post: null,
          comments: []
        }
      },
      methods: {
        query: function () {
          let _this = this
          PostService.getPost(this.$route.params.token, this.$route.params.slug, (data) => {
            _this.$set(_this, 'post', data.getResult())
          }, (data) => {
          })

          PostService.getPostComments(this.$route.params.token, this.$route.params.slug, null, (data) => {
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
          PostService.postPostComment(this.post, response, parent, (envelope) => {
            successcallback(envelope)
          }, (envelope) => {
            failCallback(envelope)
          })
        }
      },
      created () {
        this.query()
      },
      destroyed () {
      },
      components: {
        PostItem,
        CommentItem
      }
    }
</script>