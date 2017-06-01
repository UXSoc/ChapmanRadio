<template>
    <div class="col-md-2 lt-trackbox">
        <img :src="image">
        <div class="lt-text">
            <p class="lt-title">{{title}}</p>
            <p class="lt-artist">{{artiest}}</p>
            <p class="lt-minsago">{{time}}</p>
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
    artiest: {
      type: String,
      default: ''
    },
    time: {
      type: String,
      default: ''
    },
    image: {
      type: String,
      default: ''
    }
  },
  methods: {}
}
</script>