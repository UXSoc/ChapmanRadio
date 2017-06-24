<template>
    <nav aria-label="Page navigation" v-if="pagination">
        <ul class="pagination">
            <li v-bind:class="{disabled : currentPage == 0}">
                <a href="#" aria-label="Previous" v-on:click.prevent="prevPage()">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li v-for="n in findBottomRange()" v-bind:class="{active : currentPage == n}"><a href="#" v-on:click.prevent="switchPage(n)">{{n}}</a></li>
            <li v-if="currentPage + bottomRange < maxPages()"> <a>...</a></li>
            <li v-if="currentPage + bottomRange < maxPages()" v-for="n in findTopRange()"   v-bind:class="{active : currentPage == n}">
                <a href="#" v-on:click.prevent="switchPage(n)">{{n}}</a>
            </li>
            <li v-bind:class="{disabled : currentPage === maxPages()}">
                <a href="#" aria-label="Next" v-on:click.prevent="nextPage()">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
  export default{
    props: {
      perPage: {
        type: Number,
        default: 0
      },
      total: {
        type: Number,
        default: 0
      },
      currentPage: {
        type: Number,
        default: 0
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
    computed: {
      maxPages: function () {
        if (this.total === this.perPage) {
          return 0
        }
        return Math.ceil(this.total / this.perPage)
      }
    },
    methods: {
      findBottomRange: function () {
        let r = []
        let start = (this.currentPage - this.bottomRange) < 0 ? 0 : this.currentPage - this.bottomRange
        for (let i = start; i < start + (this.bottomRange + this.bottomRange); i++) {
          if (i > this.pagination.getMaxPage()) { break }
          r.push(i)
        }
        return r
      },
      findTopRange: function () {
        let r = []
        let bottom = ((this.currentPage - this.bottomRange) < 0 ? 0 : this.currentPage) + this.bottomRange
        for (let i = this.pagination.getMaxPage(); i > this.pagination.getMaxPage() - this.topRange; i--) {
          if (i < bottom) { break }
          r.push(i)
        }
        return r.reverse()
      },
      nextPage: function () {
        this.$emit('onPageChange', this.currentPage + 1)
      },
      prevPage: function () {
        this.$emit('onPageChange', this.currentPage - 1)
      },
      switchPage: function (n) {
        this.$emit('onPageChange', n)
      },
      maxPages: function () {
        if (this.total === this.perPage) {
          return 0
        }
        return Math.ceil(this.total / this.perPage)
      }
    }
  }
</script>

<style>
    .pagination {
        margin: 0 0 0 0;
    }
</style>