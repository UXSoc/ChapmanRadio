<template>
    <div class="post-entry" v-if="post">
        <h2><router-link :to="post.getRoute()" :exact="true" tag="a">{{post.name}}</router-link></h2>
        <div v-html="post.content"></div>
        <slot></slot>
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