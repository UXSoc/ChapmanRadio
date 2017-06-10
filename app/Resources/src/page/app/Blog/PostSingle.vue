<template>
    <div>
        <post v-if="post" :post="post"></post>
        <comment style="background:pink" v-if="comments"  v-for="comm in comments" :comment="comm" :key="comm.getToken()"></comment>
    </div>
</template>

<script>
    import PostService from '../../../service/postService'
    import Post from '../../../components/Post.vue'
    import Comment from '../../../components/Comment.vue'
    export default{
      data () {
        return {
          post: null,
          comments: []
        }
      },
      methods: {
        query: function (token,slug) {
          let _this = this
          PostService.getPost(this.$route.params.token, this.$route.params.slug, (data) => {
            _this.$set(_this, 'post', data.getResult())
          }, (data) => {
          })

          PostService.getPostComments(this.$route.params.token, this.$route.params.slug, null, (data) => {
            _this.$set(_this, 'comments', data.getResult())
          }, (data) => {
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