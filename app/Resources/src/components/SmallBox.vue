<template>
    <div class="col-md-3 wnb">
        <router-link class="wn-box" :to="post.getRoute()" :exact="true" tag="a">
            <img src="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg">
            <p class="wn-box-title wn-sb-title">{{ post.name}}</p>
            <p class="wn-box-desc wn-sb-desc">{{ post.excerpt }}</p>
        </router-link>
    </div>
</template>

<script>
  import Post from '../entity/post'
  export default{
    data () {
      return {}
    },
    props: {
      post: {
        type: Post,
        default: new Post({})
      }
    },
    methods: {
    }
  }
</script>