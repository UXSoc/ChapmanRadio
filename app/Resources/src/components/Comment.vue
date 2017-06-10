<template>
    <div class="media" v-if="comment">
        <div class="media-left">
        </div>
        <div class="media-body">
            {{comment.getConent()}}
            <button v-if="owner" v-on:click.prevent="edit = true" >Edit</button>
            <p>username: {{comment.getUser().getUsername()}}</p>

            <comment v-if="edit" v-for="(comm, index) in comment.getChildren()" :comment="comm" :key="comm.getToken()"></comment>
            <div v-else>edit!!</div>

            <button v-on:click.prevent="respond=true">Respond</button>
            <div v-if="respond">
                <textarea class="markdown-editor"></textarea>
            </div>


        </div>
    </div>
</template>

<script>
    import User from '../entity/user'
    import Comment from '../entity/comment'
    import simplemed from 'simplemde/src/js/simplemde'
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
        }
      },
      watch: {
        '$auth.status': 'updateCommentStatus'
      },
      data () {
        return {
          owner: false,
          edit: false,
          respond: false
        }
      }
    }
</script>