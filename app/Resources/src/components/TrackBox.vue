<template>
    <div class="lt-trackbox">
        <img src="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg">
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
        }
    },
    methods: {
    }
  }
</script>