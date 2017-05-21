<template>
    <div class="table-responsive">
        <div>
            <ul class="list-inline pull-left">
                <slot name="header-bar-left"></slot>
            </ul>
            <ul class="list-inline pull-right">
                <slot name="header-bar-right"></slot>
            </ul>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th v-if="multiSelect"></th>
                <th v-for="f in format">
                    <a v-on:click="switchColumnSort(f.column)">
                        {{f.column}}
                        <i v-if="!(f.column in sort) |  sort[f.column] == 'default'" class="fa fa-sort"
                           aria-hidden="true"></i>
                        <i v-else-if="sort[f.column] == 'desc'" class="fa fa-sort-desc" aria-hidden="true"></i>
                        <i v-else-if="sort[f.column] == 'asc'" class="fa fa-sort-asc" aria-hidden="true"></i>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="d in data">
                    <td v-if="multiSelect"></td>

                    <td v-for="f in format">
                        {{ d[f.column] }}
                    </td>

                </tr>
            </tbody>
        </table>
        <div class="pull-left">
            show
            <select class="input-sm" v-model="perPage">
                <option :value="10">10</option>
                <option :value="20">20</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
            </select>
            entires
        </div>
        <pagination class="pull-right"
                @pageChange="pageChange"
                v-if="enablePagination"
                :currentPage="currentPage"
                :total="total"
                :entriesPerPage="perPage"></pagination>
    </div>

</template>

<script>
  import Pagination from '../../components/Pagination.vue'
  export default{
    props: {
      format: {
        type: Array,
        default: function () {
          return null
        }
      },
      enablePagination: {
        type: Boolean,
        default: function () {
          return true
        }
      },
      source: {
        type: String,
        default: function () {
          return ''
        }
      },
      numRows: {
        type: Number,
        default: function () {
          return 10
        }
      },
      multiSelect: {
        type: Boolean,
        default: function () {
          return false
        }
      },
      indexColumn: {
        type: String,
        default: function () {
          return 'id'
        }
      },
      additionalParameters: {
        type: Object,
        default: function () {
          return []
        }
      }
    },
    data () {
      return {
        data: null,
        total: 0,
        sort: {},
        currentPage: 0,
        perPage: 10
      }
    },
    methods: {
      switchColumnSort: function (column) {
        if (!(column in this.sort)) {
          this.$set(this.sort, column, 'desc')
        } else {
          switch (this.sort[column]) {
            case 'desc':
              this.$set(this.sort, column, 'asc')
              break
            case 'asc':
              this.$set(this.sort, column, 'default')
              break
            case 'default':
              this.$set(this.sort, column, 'desc')
              break
          }
        }
        this.query()
      },
      pageChange: function (page) {
        this.currentPage = page
        this.query()
      },
      query: function () {
        let temp = this
        let result = {
          sort: this.sort,
          currentPage: this.currentPage,
          perPage: this.perPage
        }
        for (let k in this.additionalParameters) {
          result[k] = this.additionalParameters[k]
        }
        axios.post(this.source, result).then(function (response) {
          temp.$set(temp, 'data', response.data.result)
          temp.$set(temp, 'total', response.data.count)
        }).catch(function (error) {
            console.log(error)
        })
      }
    },
    created: function () {
      this.query()
    },
    watch: {
      perPage: function (val) {
        this.query()
      },
      additionalParameters: function (val) {
        this.query()
      }

    },
    components: {
      Pagination
    }
  }
</script>

<style>


</style>