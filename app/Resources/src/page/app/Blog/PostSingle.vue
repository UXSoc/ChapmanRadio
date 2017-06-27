<template>
    <div>
        <post-item v-if="post" :post="post">
            <comment-item v-if="comments"  v-for="comm in comments" :comment="comm" :key="comm.token" @respond="respondComment" @edit="editComment"></comment-item>
        </post-item>
    </div>
</template>

<script>
    /* @flow */
    import PostService from '../../../service/postService'
    import PostItem from '../../../components/PostItem.vue'
    import CommentItem from '../../../components/CommentItem.vue'
    import CommentService from '../../../service/commentService'
    import Post from '../../../entity/post'
    import Comment from '../../../entity/comment'
    import Form from '../../../entity/form'

    export default{
      data () : { post:Post, comments: [Comment] } {
        return {
          post: null,
          comments: []
        }
      },
      methods: {
        query: function () {
          const _this = this
          PostService.getPost(this.$route.params.token, this.$route.params.slug, (post) => {
            _this.$set(_this, 'post', post)
            PostService.getPostComments(post, null, (comments) => {
              _this.$set(_this, 'comments', comments)
            })
          })
        },
        editComment (markdown: string, comment: Comment, commentItem: CommentItem) {
          CommentService.patchComment(comment, markdown, (resp) => {
            if (resp instanceof Form) {
            }
            if (resp instanceof Comment) {
              comment.content = resp.content
            }
          })
        },
        respondComment (markdown: string, comment: Comment, commentItem: CommentItem) {
          PostService.postPostComment(this.post, markdown, comment, (resp) => {
            if (resp instanceof Form) {
            }
            if (resp instanceof Comment) {
              comment.unshift(resp)
            }
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