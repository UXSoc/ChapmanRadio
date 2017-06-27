<template>
    <div>
        <show-item :show="show">
            <comment-item v-if="comments"  v-for="comm in comments" :comment="comm" :key="comm.token" @respond="respondComment" @edit="editComment"></comment-item>
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
  import Form from '../../../entity/form'
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
          _this.$set(_this, 'show', data)
          ShowService.getShowComments(data, null, (data) => {
            _this.$set(_this, 'comments', data)
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
        ShowService.postPostComment(this.post, markdown, comment, (resp) => {
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
    components: {
      ShowItem,
      CommentItem
    }
  }
</script>