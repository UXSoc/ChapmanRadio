<template>
    <div>
        <post v-if="post" :post="post">
            <comment v-if="comments"  v-for="comm in comments" :comment="comm" :key="comm.getToken()" :respondCallback="respondCallback" :editCallback="editCallback"></comment>
        </post>
    </div>
</template>

<script>
    import PostService from '../../../service/postService'
    import Post from '../../../components/Post.vue'
    import Envelope from '../../../entity/envelope'
    import Comment from '../../../components/Comment.vue'
    import CommentService from '../../../service/commentService'

    export default{
      data () : { post:Post, comments: [Comment] } {
        return {
          post: null,
          comments: []
        }
      },
      methods: {
        query: function (token, slug) {
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
        Post,
        Comment
      }
    }
</script>