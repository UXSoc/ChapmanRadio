<template>
    <div class="showoftheweek">
        <div class="row row-eq-height marginfix">
            <div class="col-md-7 nopadding">
                <img src="./../../img/showoftheweek.jpg">
            </div>
            <div class="col-md-5 sotw-text nopadding">
                <p class="sotw-showname">{{ show_name }}</p>
                <p class="sotw-djnames">{{ dj_names }}</p>
                <p class="sotw-desc">{{ show_desc }}</p>
                <a class="btn btn-full btncenter" href="#">VIEW EPISODES</a>
            </div>
        </div>
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
        dj_names: {
            type: String,
            default: ''
        },
        show_desc: {
            type: String,
            default: ''
        }
    },
    methods: {
    }
  }
</script>