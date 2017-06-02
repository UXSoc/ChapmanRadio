<template>
    <div class="col-md-6 wn-widebox">
        <img src="../../public/img/dj-wide.jpeg">
        <div class="wn-widebox-text">
            <p class="wn-box-title wn-wb-title">{{ title }}</p>
            <p class="wn-box-desc wn-wb-desc">{{ description }}</p>
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