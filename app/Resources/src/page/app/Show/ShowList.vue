<template>
    <div>
        <h2 class="cr_header">Shows</h2>
        <div class="row-resp">
            <template v-if="data.length > 0" v-for="(item, index) in data">
                <showcase-box :show="item"></showcase-box>
            </template>
            <div v-else>
                <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</template>

<script>
    /* @flow */
    import ShowcaseBox from '../../../components/ShowcaseBox.vue'
    import ShowService from '../../../service/showService'
    import Show from '../../../entity/show'
    import Pagination from '../../../entity/pagination'
    import $ from 'jquery'

    export default{
      data () {
        return {
          pagination: null,
          data: []
        }
      },
      methods: {
        query: function (page) {
          ShowService.getShows(page, (result: Pagination<Show>) => {
            this.$set(this, 'pagination', result)
            this.$set(this, 'data', this.data.concat(result.result))
          })
        },
        handleScroll () {
          if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if (this.pagination) {
              if (this.pagination.currentPage <= this.pagination.maxPage) {
                this.query(this.pagination.nextPage)
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