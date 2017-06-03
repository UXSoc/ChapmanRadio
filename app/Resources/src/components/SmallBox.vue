<template>
    <div class="col-md-3">
        <a class="wn-smallbox" @mouseover="active = true" @mouseout="active = false" :href="post_url">
            <img :src="image_url">
            <div class="wn-smallbox-text" :class="backgroundChange">
                <p class="wn-box-title wn-sb-title">{{ title }}</p>
                <p class="wn-box-desc wn-sb-desc">{{ description }}</p>
            </div>
        </a>
    </div>
</template>

<script>
  export default{
      data () {
          return {
              active: false
          }
      },
      computed: {
          backgroundChange: function() {
              return {
                  'wn-smallbox-text': !this.active,
                  'wn-smallbox-text-hover': this.active
              };
          }
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
        post_url: {
            type: String,
            default: '#'
        }
    },
    methods: {
    }
  }
</script>