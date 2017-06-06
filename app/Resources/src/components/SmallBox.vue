<template>
    <div class="col-md-3 wnb">
        <router-link class="wn-box" :to="uri" :exact="true" tag="a">
            <img :src="image_url">
            <p class="wn-box-title wn-sb-title">{{ title }}</p>
            <p class="wn-box-desc wn-sb-desc">{{ description }}</p>
        </router-link>
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
        image_url: {
            type: String,
            default: ''
        },
        uri: {
            type: Object,
            default: {}
        }
    },
    methods: {
    }
  }
</script>