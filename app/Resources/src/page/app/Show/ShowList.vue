<template>
    <div>
        <h2 class="cr_header">Shows</h2>
        <div class="row-resp">
            <template v-for="(item, index) in data">
                <showcase-box :show="item"></showcase-box>
            </template>
            <div v-if="loading">
                <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</template>

<script>
    import ShowcaseBox from '../../../components/ShowcaseBox.vue'
    import ShowService from '../../../service/showService'
    import Show from '../../../entity/show'
    import Pagination from '../../../entity/pagination'
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
          ShowService.getShows(page, (data) => {
            _this.loading = false
            let pagination : Pagination = data.getResult()
            let shows: [Show] = pagination.getResult()
            let result = _this.data
            result = result.concat(shows)
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
      watch: {},
      components: {
        ShowcaseBox
      },
      created () {
        this.query(0)
        window.addEventListener('scroll', this.handleScroll)
      },
      destroyed () {
        window.removeEventListener('scroll', this.handleScroll)
      }
    }
</script>