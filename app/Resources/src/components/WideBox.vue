<template>
    <div class="col-md-6">
        <a class="wn-widebox" @mouseover="active = true" @mouseout="active = false" :href="post_url">
            <img :src="image_url">
            <div class="wn-widebox-text" :class="backgroundChange">
                <p class="wn-box-title wn-wb-title">{{ title }}</p>
                <p class="wn-box-desc wn-wb-desc">{{ description }}</p>
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
                'wn-widebox-text': !this.active,
                'wn-widebox-text-hover': this.active
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