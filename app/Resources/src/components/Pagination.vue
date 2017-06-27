<template>
    <nav aria-label="Page navigation" v-if="pagination">
        <ul class="pagination">
            <li v-bind:class="{disabled : pagination.currentPage == 0}">
                <a href="#" aria-label="Previous" v-on:click.prevent="prevPage()">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li v-for="n in findBottomRange()" v-bind:class="{active : pagination.currentPage == n}"><a href="#" v-on:click.prevent="switchPage(n)">{{n}}</a></li>
            <li v-if="pagination.currentPage + bottomRange < pagination.maxPage"> <a>...</a></li>
            <li v-if="pagination.currentPage + bottomRange < pagination.maxPage" v-for="n in findTopRange()"   v-bind:class="{active : pagination.currentPage == n}">
                <a href="#" v-on:click.prevent="switchPage(n)">{{n}}</a>
            </li>
            <li v-bind:class="{disabled : pagination.currentPage == pagination.maxPage}">
                <a href="#" aria-label="Next" v-on:click.prevent="nextPage()">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
  import Pagination from './../entity/pagination'
  export default{
    props: {
      pagination: {
        type: Pagination,
        default: null
      },
      topRange: {
        type: Number,
        default: 3
      },
      bottomRange: {
        type: Number,
        default: 3
      }
    },
    data () {
      return {
      }
    },
    methods: {
      findBottomRange: function () {
        const r = []
        const start = (this.pagination.currentPage - this.bottomRange) < 0 ? 0 : this.pagination.currentPage - this.bottomRange
        for (let i = start; i < start + (this.bottomRange + this.bottomRange); i++) {
          if (i > this.pagination.maxPage) { break }
          r.push(i)
        }
        return r
      },
      findTopRange: function () {
        const r = []
        const bottom = ((this.pagination.currentPage - this.bottomRange) < 0 ? 0 : this.pagination.currentPage) + this.bottomRange
        for (let i = this.pagination.maxPage; i > this.pagination.maxPage - this.topRange; i--) {
          if (i < bottom) { break }
          r.push(i)
        }
        return r.reverse()
      },
      nextPage: function () {
        this.$emit('onPageChange', this.pagination.currentPage + 1)
      },
      prevPage: function () {
        this.$emit('onPageChange', this.pagination.currentPage - 1)
      },
      switchPage: function (n) {
        this.$emit('onPageChange', n)
      }
    }
  }
</script>

<style>
    .pagination {
        margin: 0 0 0 0;
    }
</style>