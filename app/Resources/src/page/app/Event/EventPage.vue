<template>
    <div class="container-fluid">
        <h2 style="margin-left:15px;margin-bottom:0;" class="cr_header">Events</h2>
        <div class="row-resp">
            <template v-for="(item, index) in data">
                <wide-box v-if="index == 0" :post="item"></wide-box>
                <small-box v-else :post="item"></small-box>
            </template>
       </div>
       <div v-if="loading"> Loading </div>
    </div>
</template>

<script>
    /* @flow */
    import WideBox from '../../../components/WideBox.vue'
    import PostService from '../../../service/postService'
    import SmallBox from '../../../components/SmallBox.vue'
    import Pagination from '../../../entity/pagination'
    import Post from '../../../entity/post'
    import $ from 'jquery'
    export default{
      data () {
        return {
          pagination: null,
          data: [],
          loading: false
        }
      },
      methods: {
        query: function (page) {
          let _this = this
          _this.loading = true
          PostService.getPosts(page, (data) => {
            _this.loading = false
            let pagination : Pagination = data.getResult()
            let posts: [Post] = pagination.getResult()
            let result = _this.data
            result = result.concat(posts)
            _this.$set(_this, 'data', result)
            _this.$set(_this, 'pagination', pagination)
          }, (data) => {
          }, {tags: ['event']})
        },
        handleScroll () {
          if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if (!this.loading) {
              if (this.page <= this.maxPage) {
                this.query(this.pagination.getNextPage())
              }
            }
          }
        }
      },
      watch: {
      },
      created () {
        this.query(0)
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