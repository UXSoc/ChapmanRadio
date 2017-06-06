<template>
    <div class="container">
        <h2 class="cr_header">Events</h2>
        <div class="row-resp">
            <template v-for="(item, index) in data">
                <wide-box v-if="index == 0" :title="item.name" :uri="{name: 'post_single', params: { token:item.token, slug:item.slug } }" :description="item.excerpt" image_url="/bundles/public/img/dj-wide.jpeg"></wide-box>
                <small-box v-else :title="item.name" :uri="{name: 'post_single', params: { token:item.token, slug:item.slug } }" :description="item.excerpt" image_url="/bundles/public/img/dj-wide.jpeg"></small-box>
            </template>
       </div>
       <div v-if="loading"> Loading </div>
    </div>
</template>

<script>
    import WideBox from '../../components/WideBox.vue'
    import SmallBox from '../../components/SmallBox.vue'
    import axios from 'axios'
    export default{
      data () {
        return {
          data: [],
          page: 0,
          maxPage: 0,
          loading: false
        }
      },
      methods: {
        query: function () {
          let qs = require('qs')
          let _this = this
          _this.loading = true
          axios.get(Routing.generate('get_posts') + '?' + qs.stringify({page: this.page, tag: ['event']})).then(function (response) {
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
                this.query()
              }
            }
          }
        }
      },
      watch: {
      },
      created () {
        this.query()
        window.addEventListener('scroll', this.handleScroll)
      },
      destroyed () {
        window.removeEventListener('scroll', this.handleScroll)
      },
      components: {
        WideBox,
        SmallBox
      }
    }
</script>