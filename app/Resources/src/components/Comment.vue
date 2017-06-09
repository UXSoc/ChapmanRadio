<template>
    <div class="media" v-if="comment">
        <div class="media-left">
        </div>
        <div class="media-body">
            {{comment.getConent()}}
            <div style="background: red">
                <comment v-for="(comm, index) in comment.getChildren()" :comment="comm" :key="comm.getToken()"></comment>
            </div>
        </div>
    </div>
</template>

<script>
    import Comment from '../entity/comment'
    export default{
      name: 'comment',
      props: {
        comment: {
          type: Comment,
          default: null
        }
      },
      data () {
          return {
          }
      }
    }
</script>