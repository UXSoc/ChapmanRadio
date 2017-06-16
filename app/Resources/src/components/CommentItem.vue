<template>
    <div v-if="comment">
        <div class="media-left">
        </div>
        <div>
            <p>username: {{item.getUser().getUsername()}}</p>

            <template v-if="!edit"> <div v-html="$options.filters.markdown(item.getContent())"></div> </template>
            <button v-if="!edit && owner" v-on:click.prevent="editComment()">Edit</button>
            <comment-editor :visible="edit" @submit="onEditSubmit" :content="item.getContent()" ></comment-editor>

            <button v-if="!respond" v-on:click.prevent="respondToComment()">Respond</button>
            <comment-editor :visible="respond" @submit="onRespondSubmit"></comment-editor>

            <comment v-for="(comm, index) in item.getChildren()" :editCallback="editCallback"  :respondCallback="respondCallback" :comment="comm" :key="comm.getToken()"></comment>
        </div>
    </div>
</template>

<script>
    import User from '../entity/user'
    import Comment from '../entity/comment'
    import { EventBus } from './../eventBus'
    import CommentEditor from './quill/commentEditor'
    import Envelope from './../entity/envelope'
    import Markdown from './../mixins/markdown'

    export default{
      mixins: [Markdown],
      name: 'comment',
      props: {
        comment: {
          type: Comment,
          default: null
        },
        respondCallback: {
          type: Function,
          default: (parent: Comment, response: string, successcallback: (e: Envelope<Comment>) => void, failCallback: (e: Envelope) => void) => {}
        },
        editCallback: {
          type: Function,
          default: (current: Comment, response: string, successcallback: (e: Envelope<Comment>) => void, failCallback: (e: Envelope) => void) => {}
        }
      },
      methods: {
        onRespondSubmit (markdown: string) {
          let _this = this
          this.respondCallback(this.comment, markdown, (e : Envelope<Comment>) => {
            _this.item.shift(e.getResult())
            _this.$set(_this, 'item', _this.item)
            _this.$set(_this, 'respond', false)
          }, (e: Envelope) => {
          })
        },
        onEditSubmit (markdown: string) {
          let _this = this
          this.editCallback(this.comment, markdown, (e : Envelope<Comment>) => {
            _this.item.setContent(e.getResult().getContent())
            _this.$set(_this, 'item', _this.item)
            _this.$set(_this, 'edit', false)
          }, (e: Envelope) => {
          })
        },
        respondToComment () {
          this.$set(this, 'respond', true)
          EventBus.$emit('comment-respond', this.item.getToken())
        },
        editComment () {
          this.$set(this, 'edit', true)
          EventBus.$emit('comment-edit', this.item.getToken())
        },
        onCommentEdit (token) {
          if (this.item.getToken() !== token) {
            this.$set(this, 'respond', false)
            this.$set(this, 'edit', false)
          } else {
            this.$set(this, 'respond', false)
          }
        },
        onCommentRespond (token) {
          if (this.item.getToken() !== token) {
            this.$set(this, 'respond', false)
            this.$set(this, 'edit', false)
          } else {
            this.$set(this, 'edit', false)
          }
        },
        updateStatus () {
          const user: User = this.$auth.getStatus()
          this.$set(this, 'owner', user.getToken() === this.item.getUser().getToken())
        }
      },
      watch: {
        '$auth.status': 'updateStatus',
        'comment': (value) => {
          this.$set(this, 'item', value)
          this.updateStatus()
        }
      },
      created () {
        this.item = this.comment
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
          respond: false,
          item: new Comment({})
        }
      },
      components: {
        CommentEditor
      }
    }
</script>