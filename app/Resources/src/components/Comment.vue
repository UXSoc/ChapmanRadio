<template>
    <div class="media" v-if="comment">
        <div class="media-left">
        </div>
        <div class="media-body">
            {{comment.getConent()}}
            <button v-if="owner" v-on:click.prevent="editcomment ()" >Edit</button>
            <p>username: {{comment.getUser().getUsername()}}</p>

            <comment v-if="!edit" v-for="(comm, index) in comment.getChildren()" :comment="comm" :key="comm.getToken()"></comment>
            <div v-else>
                <!--<textarea class="markdown-editor" ref="edit" ></textarea>-->
            </div>

            <button v-on:click.prevent="respondToComment()">Respond</button>
            <div v-if="respond">

            </div>
            <div ref="respond" >
            </div>

        </div>
    </div>
</template>

<script>
    import User from '../entity/user'
    import Comment from '../entity/comment'
    import { EventBus } from './../eventBus'
    import Quill from './../quill'
    export default{
      name: 'comment',
      props: {
        comment: {
          type: Comment,
          default: null
        }
      },
      methods: {
        updateCommentStatus () {
          const user: User = this.$auth.getStatus()
          if (user.getToken() === this.comment.getUser().getToken()) {
            this.owner = true
          } else {
            this.owner = false
          }
        },
        editcomment () {
          EventBus.$emit('comment-edit', this.comment.getToken())
        },
        respondToComment () {
          this.respond = true
          this.quill = new Quill(this.$refs.respond, {
            modules: {
              toolbar: [
                [{ header: [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['image', 'code-block']
              ]
            },
            placeholder: 'Compose an epic...',
            theme: 'snow'  // or 'bubble'
          })

          EventBus.$emit('comment-edit', this.comment.getToken())
        },
        onEditor (payload) {
          console.log('derp')
        }
      },
      watch: {
        '$auth.status': 'updateCommentStatus'
      },
      created () {
        EventBus.$on('comment-edit', this.onEditor)
      },
      destroyed () {
        EventBus.$off('comment-edit', this.onEditor)
      },
      data () {
        return {
          owner: false,
          edit: false,
          respond: false,
          quill: null
        }
      }
    }
</script>