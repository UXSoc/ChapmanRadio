<template>
    <div v-if="comment">
        <div class="media-left">
        </div>
        <div v-if="comment">
            <p>username: {{comment.user.username}}</p>

            <template v-if="!edit"> <div v-html="$options.filters.markdown(comment.content)"></div> </template>
            <button v-if="!edit && owner" v-on:click.prevent="editComment()">Edit</button>
            <comment-editor :visible="edit" @submit="onEditSubmit" :content="comment.content" ></comment-editor>

            <button v-if="!respond" v-on:click.prevent="respondToComment()">Respond</button>
            <comment-editor :visible="respond" @submit="onRespondSubmit"></comment-editor>

            <comment v-for="(comm, index) in comment.children" :comment="comm" :key="comm.token" @respond="passSubmit" @edit="passEdit"></comment>
        </div>
    </div>
</template>

<script>
    import User from '../entity/user'
    import Comment from '../entity/comment'
    import { EventBus } from './../eventBus'
    import CommentEditor from './quill/commentEditor'
    import Markdown from './../mixins/markdown'

    export default{
      mixins: [Markdown],
      name: 'comment',
      props: {
        comment: {
          type: Comment,
          default: null
        }
      },
      methods: {
        passSubmit (markdown: string, comment: Comment, CommentItem: this) {
          this.$emit('respond', markdown, comment, CommentItem)
        },
        passEdit (markdown: string, comment: Comment, CommentItem: this) {
          this.$emit('edit', markdown, comment, CommentItem)
        },
        onRespondSubmit (markdown: string) {
          this.$emit('respond', markdown, this.comment, this)
        },
        onEditSubmit (markdown: string) {
          this.$emit('edit', markdown, this.comment, this)
        },
        respondToComment () {
          this.$set(this, 'respond', true)
          EventBus.$emit('comment-respond', this.comment.token)
        },
        editComment () {
          this.$set(this, 'edit', true)
          EventBus.$emit('comment-edit', this.comment.token)
        },
        onCommentEdit (token) {
          if (this.comment.token !== token) {
            this.$set(this, 'respond', false)
            this.$set(this, 'edit', false)
          } else {
            this.$set(this, 'respond', false)
          }
        },
        onCommentRespond (token) {
          if (this.comment.token !== token) {
            this.$set(this, 'respond', false)
            this.$set(this, 'edit', false)
          } else {
            this.$set(this, 'edit', false)
          }
        },
        updateStatus () {
          const user: User = this.$auth.getStatus()
          this.$set(this, 'owner', user.token === this.comment.user.token)
        }
      },
      watch: {
        '$auth.status': 'updateStatus',
        'comment': function (value) {
          this.updateStatus()
        }
      },
      created () {
        this.updateStatus()
        EventBus.$on('comment-edit', this.onCommentEdit)
        EventBus.$on('comment-respond', this.onCommentRespond )
      },
      destroyed () {
        EventBus.$off('comment-edit', this.onCommentEdit)
        EventBus.$off('comment-respond', this.onCommentRespond )
      },
      data () {
        return {
          owner: false,
          edit: false,
          respond: false
        }
      },
      components: {
        CommentEditor
      }
    }
</script>