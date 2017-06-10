<template>
    <div class="lt-trackbox">
        <img :src="img_url">
        <div class="lt-text">
            <p class="lt-title">{{ track_name }}</p>
            <p class="lt-artist">{{ artist }}</p>
            <p class="lt-minsago">{{ mins_ago }}m ago</p>
        </div>
    </div>
</template>

<script>
  export default{
    data () {
      return {}
    },
    props: {
      track_name: {
        type: String,
        default: ''
      },
      artist: {
        type: String,
        default: ''
      },
      mins_ago: {
        type: String,
        default: ''
      },
      img_url: {
        type: String,
        default: ''
      }
    },
    methods: {
    }
  }
</script>