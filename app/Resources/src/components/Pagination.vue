<template>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li v-bind:class="{disabled : currentPage == 0}">
                <a href="#" aria-label="Previous" v-on:click="prevPage()">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li v-for="n in findBottomRange()" v-bind:class="{active : currentPage == n}"><a href="#" v-on:click.prevent="switchPage(n)">{{n}}</a></li>
            <li v-if="currentPage + bottomRange < numberOfPages"> <a>...</a></li>
            <li v-if="currentPage + bottomRange < numberOfPages" v-for="n in findTopRange()"   v-bind:class="{active : currentPage == n}"><a href="#" v-on:click.prevent="switchPage(n)">{{n}}</a></li>
            <li v-bind:class="{disabled : currentPage == numberOfPages}">
                <a href="#" aria-label="Next" v-on:click="nextPage()">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
  export default{
    props: {
      total: {
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

      },
      entriesPerPage: {
        type: Number,
        default: 10
      },
      currentPage: {
        type: Number,
        default: 10
      }
    },
    data () {
      return {
        numberOfPages: 0
      }
    },
    methods: {
      findBottomRange: function () {
        let r = []
        let start = (this.currentPage - this.bottomRange) < 0 ? 0 : this.currentPage - this.bottomRange
        for (let i = start; i < start + (this.bottomRange+this.bottomRange); i++) {
          if (i > this.numberOfPages) { break }
          r.push(i)
        }
        return r
      },
      findTopRange: function () {
        let r = []
        let bottom = ((this.currentPage - this.bottomRange) < 0 ? 0 : this.currentPage) + this.bottomRange
        for (let i = this.numberOfPages; i > this.numberOfPages - this.topRange; i--) {
          if (i < bottom) { break }
          r.push(i)
        }
        return r.reverse()
      },
      nextPage: function () {
        this.$emit('pageChange', this.currentPage + 1)
      },
      prevPage: function () {
        this.$emit('pageChange', this.currentPage - 1)
      },
      switchPage: function (pageNumber) {
        this.$emit('pageChange', pageNumber)
      },
      updatePageCount: function () {
        this.numberOfPages = Math.ceil(this.total / this.entriesPerPage)
      }
    },
    watch: {
      entriesPerPage: function () {
        this.updatePageCount()
      },
      total: function () {
        this.updatePageCount()
      }
    }
  }
</script>

<style>
    .pagination {
        margin: 0 0 0 0;
    }
</style>