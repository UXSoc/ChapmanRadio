<template>
    <div class="post-entry" v-if="post">
        <h2><router-link :to="post.getRoute()" :exact="true" tag="a">{{post.getName()}}</router-link></h2>
        <div v-html="post.getContent()"></div>
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