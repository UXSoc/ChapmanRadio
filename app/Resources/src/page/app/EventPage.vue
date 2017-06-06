<template>
    <div class="container">
        <h2 class="cr_header">Events</h2>
        <div class="row">
            <template v-for="(item, index) in data">
                <wide-box v-if="index == 0" :title="item.name" :description="item.excerpt" image_url="/bundles/public/img/dj-wide.jpeg"></wide-box>
                <small-box v-else :title="item.name" :description="item.excerpt" image_url="/bundles/public/img/dj-wide.jpeg"></small-box>
            </template>
            <small-box title="Muscochella Hype Playlist" description="Here's what went down when our favorite indie artists took to the stage." image_url="/bundles/public/img/dj-wide.jpeg"></small-box>
        </div>
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
          page: 0
        }
      },
      methods: {
        update: function (page) {
          let qs = require('qs')
          let _this = this
          axios.get(Routing.generate('get_posts') + '?' + qs.stringify({page: page, tag: ['event']})).then(function (response) {
            let pageinator = response.data.data
            let result = _this.result.join(pageinator.result)
            _this.$set(_this, 'data', result)
          }).catch(function (error) {
          })
        },
        handleScroll() {
          if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            this.page += 1
            this.update(this.page)
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
        WideBox,
        SmallBox
      }
    }
</script>