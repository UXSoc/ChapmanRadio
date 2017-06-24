<template>
    <div v-if="dataTable">
        <table class="table">
            <thead>
                <tr>
                    <slot name="column" v-for="c in columns" :column="c" :sort="dataTable.getColumnSort(c)">test</slot>
                </tr>
            </thead>
            <tbody>
                <slot name="result" v-for="result in getPayload()" :item="result"></slot>
            </tbody>
        </table>
        <select v-model="numEntries">
            <option v-for="r in range" :value="r">{{r}}</option>
        </select>
        <pagination v-if="hasPagination()" @onPageChange="triggerPageChange" :pagination="dataTable.getPayload()"></pagination>
    </div>
</template>

<script>
  import Paginator from '../entity/pagination'
  import Pagination from './Pagination.vue'
  import Datatable from './../entity/dataTable'
  export default{
    props: {
      dataTable: {
        type: Datatable,
        default: null
      },
      range: {
        type: Array,
        default: () => [10, 20, 50, 100, 200]
      },
      columns: {
        type: Array,
        default: () => []
      }
    },
    data () {
      return {
        numEntries: 10
      }
    },
    methods: {
      triggerPageChange (value: Number) {
        this.$emit('onPageChange', value)
      },
      hasPagination () {
        return this.dataTable.getPayload() instanceof Paginator
      },
      getPayload () {
        if (this.hasPagination()) {
          return this.dataTable.getPayload().getResult()
        } else {
          return this.dataTable.getPayload()
        }
      }
    },
    watch: {
      numEntries (value) {
        this.$emit('onNumOfEntriesChange', value)
      }
    },
    components: {
      Pagination
    }
  }
</script>