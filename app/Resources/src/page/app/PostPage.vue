<template>
    <div>
        <div class="container">
            <h1 class="cr_header">Blog</h1>
            <div class="row">
                <div class="col-md-8 nopadding">
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                </div>
                <div class="col-md-4 nopadding">

                </div>
            </div>
            <div class="row">

            </div>
        </div>
    </div>
</template>

<script>
    import PostEntry from './../../components/PostEntry.vue'
    import axios from 'axios'

    export default{
      props: {
      },
      data () {
        return {
          data: [],
          page: 0,
          maxPage: 0,
          loading: false
        }
      },
      methods: {
        update: function () {
          let qs = require('qs')
          let _this = this
          _this.loading = true
          axios.get(Routing.generate('get_posts') + '?' + qs.stringify({page: this.page})).then(function (response) {
            let pageinator = response.data.data
            _this.loading = false
            _this.maxPage = Math.ceil(pageinator.count / pageinator.perPage)
            let result = _this.data.concat(pageinator.result)
            _this.$set(_this, 'data', result)
          }).catch(function (error) {
          })
        },
        handleScroll () {
          if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if (!this.loading) {
              if (this.page <= this.maxPage) {
                this.page += 1
                this.update(this.page)
              }
            }
          }
        }
      },
      watch: {
      },
      created () {
        this.update(0)
        window.addEventListener('scroll', this.handleScroll)
      },
      destroyed () {
        window.removeEventListener('scroll', this.handleScroll)
      },
      components: {
        PostEntry
      }
    }
</script>