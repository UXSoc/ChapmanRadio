<template>
    <div class="col-md-3 wn-smallbox">
        <img src="./../../img/apple-laptop.jpg">
        <div class="wn-smallbox-text">
            <p class="wn-box-title wn-sb-title">{{ title }}</p>
            <p class="wn-box-desc wn-sb-desc">{{ description }}</p>
        </div>
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
        }
    },
    methods: {
    }
  }
</script>