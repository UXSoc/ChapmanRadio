<template>
    <div class="col-md-6 wnb">
        <router-link class="wn-box" :to="to" :exact="true" tag="a">
            <img :src="image">
            <p class="wn-box-title wn-wb-title">{{ name }}</p>
            <p class="wn-box-desc wn-wb-desc">{{ excerpt }}</p>
        </router-link>
    </div>
</template>

<script>
  export default{
    data () {
      return {}
    },
    props: {
      name: {
        type: String,
        default: ''
      },
      excerpt: {
        type: String,
        default: ''
      },
      to: {
        type: {},
        default: ''
      },
      image: {
        type: String,
        default: ''
      }
    },
    methods: {
    }
  }
</script>