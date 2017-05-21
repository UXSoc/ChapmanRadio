<template>
    <div class="panel panel-default">
        <div class="panel-heading">

        </div>
        <div class="panel-body">
            <data-table v-bind:format="format" v-bind:source="source" :multiSelect="true"></data-table>
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

        ]
      }
    },
    components: {
      DataTable
    }
  }
</script>