<template>
    <div class="post-entry">
        <h2><router-link :to="post.getRoute()" :exact="true" tag="a">{{post.name}}</router-link></h2>
        <div>
            {{post.excerpt}}
        </div>
    </div>
</template>

<script>
    import Post from '../entity/post'
    export default{
      props: {
        post: {
          type: Post,
          default: new Post({})
        }
      },
      data () {
        return {}
      }
    }
</script>