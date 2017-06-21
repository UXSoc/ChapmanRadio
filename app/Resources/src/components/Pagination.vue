<template>
    <nav aria-label="Page navigation" v-if="pagination">
        <ul class="pagination">
            <li v-bind:class="{disabled : pagination.getCurrentPage() == 0}">
                <a href="#" aria-label="Previous" v-on:click.prevent="prevPage()">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li v-for="n in findBottomRange()" v-bind:class="{active : pagination.getCurrentPage() == n}"><a href="#" v-on:click.prevent="switchPage(n)">{{n}}</a></li>
            <li v-if="pagination.getCurrentPage() + bottomRange < pagination.getMaxPage()"> <a>...</a></li>
            <li v-if="pagination.getCurrentPage() + bottomRange < pagination.getMaxPage()" v-for="n in findTopRange()"   v-bind:class="{active : pagination.getCurrentPage() == n}">
                <a href="#" v-on:click.prevent="switchPage(n)">{{n}}</a>
            </li>
            <li v-bind:class="{disabled : pagination.getCurrentPage() == pagination.getMaxPage()}">
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
        let r = []
        let start = (this.pagination.getCurrentPage() - this.bottomRange) < 0 ? 0 : this.pagination.getCurrentPage() - this.bottomRange
        for (let i = start; i < start + (this.bottomRange + this.bottomRange); i++) {
          if (i > this.pagination.getMaxPage()) { break }
          r.push(i)
        }
        return r
      },
      findTopRange: function () {
        let r = []
        let bottom = ((this.pagination.getCurrentPage() - this.bottomRange) < 0 ? 0 : this.pagination.getCurrentPage()) + this.bottomRange
        for (let i = this.pagination.getMaxPage(); i > this.pagination.getMaxPage() - this.topRange; i--) {
          if (i < bottom) { break }
          r.push(i)
        }
        return r.reverse()
      },
      nextPage: function () {
        this.$emit('pageChange', this.pagination.getCurrentPage() + 1)
      },
      prevPage: function () {
        this.$emit('pageChange', this.pagination.getCurrentPage() - 1)
      },
      switchPage: function (n) {
        this.$emit('pageChange', n)
      }
    }
  }
</script>

<style>
    .pagination {
        margin: 0 0 0 0;
    }
</style>