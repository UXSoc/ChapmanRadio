<template>
    <div>
        <h1 class="cr_header">Blog</h1>
        <div class="row">
            <div class="col-md-8 nopadding">
                <template v-if="data" v-for="(item, index) in data">
                    <post-excerpt :post="item"></post-excerpt>
                </template>
                <div v-if="loading">
                    <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div class="col-md-4 nopadding">
            </div>
        </div>
        <div class="row">

        </div>
    </div>
</template>

<script>
    import PostExcerpt from '../../../components/PostExcerpt.vue'
    import PostService from '../../../service/postService'
    import Pagination from '../../../entity/pagination'
    import Post from '../../../entity/post'
    import $ from 'jquery'
    export default{
      props: {
      },
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
          })
        },
        handleScroll () {
          if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if (!this.loading) {
              if (this.pagination.getCurrentPage() <= this.pagination.getMaxPage()) {
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
        PostExcerpt
      }
    }
</script>