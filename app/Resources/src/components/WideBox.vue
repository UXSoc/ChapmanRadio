<template>
    <div class="col-md-6 wnb">
        <a class="wn-box" :href="post_url">
            <img :src="image_url">
            <p class="wn-box-title wn-wb-title">{{ title }}</p>
            <p class="wn-box-desc wn-wb-desc">{{ description }}</p>
        </a>
    </div>
</template>

<script>
  export default{
    data () {
      return {}
    },
    props: {
        title: {
            type: String,
            default: ''
        },
        description: {
            type: String,
            default: ''
        },
        post_url: {
            type: String,
            default: '#'
        },
        image_url: {
            type: String,
            default: ''
        }
    },
    methods: {
    }
  }
</script>