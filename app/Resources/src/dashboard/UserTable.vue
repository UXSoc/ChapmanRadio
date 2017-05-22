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
                    @rowSelected="userSelected"
            ></data-table>
        </div>
    </div>
</template>

<script>
  import DataTable from './table/DataTable.vue'

  export default{
    data () {
      return {
        source: Routing.generate('dashboard_users_ajax_datatable'),
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
      },
      userSelected: function (val) {
        window.open(Routing.generate('dashboard_user',{"id" : val.id}),"_self")
      }
    },
    watch: {
    },
    components: {
      DataTable
    }
  }
</script>