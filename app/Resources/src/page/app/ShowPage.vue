<template>
    <div class="container">
        <h2 class="cr_header">Shows</h2>
        <div class="row-resp">
            <showcase-box show_name="Unedited with lindseyrem" genre="Alternative/Indie" show_description="Sharing new music finds and discussing random topics for one completely unedited hour." image_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></showcase-box>
            <showcase-box show_name="Unedited with lindseyrem" genre="Alternative/Indie" show_description="Sharing new music finds and discussing random topics for one completely unedited hour." image_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></showcase-box>
            <showcase-box show_name="Unedited with lindseyrem" genre="Alternative/Indie" show_description="Sharing new music finds and discussing random topics for one completely unedited hour." image_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></showcase-box>
            <showcase-box show_name="Unedited with lindseyrem" genre="Alternative/Indie" show_description="Sharing new music finds and discussing random topics for one completely unedited hour." image_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></showcase-box>
        </div>
    </div>
</template>

<script>
    import ShowcaseBox from '../../components/ShowcaseBox.vue'
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
        query: function () {
          let qs = require('qs')
          let _this = this
          _this.loading = true
          axios.get(Routing.generate('get_shows') + '?' + qs.stringify({page: this.page})).then(function (response) {
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
        this.update(0)
        window.addEventListener('scroll', this.handleScroll)
      },
      destroyed () {
        window.removeEventListener('scroll', this.handleScroll)
      },
      components: {
        ShowcaseBox
      }
    }
</script>