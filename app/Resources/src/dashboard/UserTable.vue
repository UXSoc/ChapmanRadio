<template>
    <div class="panel panel-default">
        <div class="panel-heading">

        </div>
        <div class="panel-body">
            <input @change="update" v-model="search" type="text">
            <data-table
                    :format="format"
                    :source="source"
                    :multiSelect="true"
                    :additionalParameters="parameters"
            ></data-table>
        </div>
    </div>
</template>

<script>
  import DataTable from './table/DataTable.vue'

  export default{
    props: {
      source: {
        type: String,
        default: function () {
          return ''
        }
      }
    },
    data () {
      return {
        format: [
          {
            column: 'id',
            type: 'int',
            sortable: true
          },
          {
            column: 'name',
            type: 'string',
            sortable: true
          }

        ],
        parameters: {}
      }
    },
    methods: {
      update: function (val) {
        this.$set(this, 'parameters', {search: this.search})
      }
    },
    watch: {
    },
    components: {
      DataTable
    }
  }
</script>