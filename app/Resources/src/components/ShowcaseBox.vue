<template>
    <div class="col-md-3 wnb">
        <a class="scb-box" :href="showpage_url">
            <img :src="image_url">
            <p class="scb-text scb-text-title"><strong>{{ show_name }}</strong></p>
            <p class="scb-text"><i>{{ genre }}</i><br><br>{{ show_description }}<br><br></p>
            <!--<p class="scb-genre"><i>{{ genre }}</i></p>-->
            <!--<p class="scb-desc">{{ show_description }}</p>-->
        </a>
    </div>
</template>

<script>
  export default{
    data () {
      return {}
    },
    props: {
        show_name: {
            type: String,
            default: ''
        },
        genre: {
            type: String,
            default: ''
        },
        show_description: {
            type: String,
            default: ''
        },
        showpage_url: {
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