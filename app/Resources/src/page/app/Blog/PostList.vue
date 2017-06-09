<template>
    <div>
        <h1 class="cr_header">Blog</h1>
        <div class="row">
            <div class="col-md-8 nopadding">
                <template v-if="data" v-for="(item, index) in data">
                    <post-excerpt :post="item"></post-excerpt>
                </template>
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

    import axios from 'axios'
    export default{
      props: {
      },
      data () {
        return {
          pagination: null,
          data: null,
          loading: false
        }
      },
      methods: {
        update: function () {
          let _this = this
          _this.loading = true
          PostService.getPosts(_this.page, (data) => {
            _this.loading = false
            _this.data.concat(data.getResult())
            _this.$set(_this, 'pagination', data)
          }, (errors) => {
          })
        },
        handleScroll () {
          if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if (!this.loading) {
              if (this.pagination.currentPage() <= this.pagination.maxPage()) {
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
          PostExcerpt
      }
    }
</script>