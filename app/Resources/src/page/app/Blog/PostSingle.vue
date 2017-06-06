<template>
    <div>

    </div>
</template>

<script>
    import axios from 'axios'

    export default{
      data () {
        return {
        }
      },
      methods: {
        query: function () {
          let qs = require('qs')
          let _this = this
          axios.get(Routing.generate('get_post', {token: this.$route.params.token, slug: this.$route.params.slug})).then(function (response) {
            console.log(response.data)
          }).catch(function (error) {

          })
        }
      },
      created () {
        this.query()
//        this.token = this.$route.params.token
//        this.slug = this.$route.params.slug
      },
      destroyed () {
      },
      components: {
      }
    }
</script>