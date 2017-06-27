<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <!-- Default panel contents -->
                    <div class="panel-heading">Panel heading</div>
                    <datatable @onNumOfEntriesChange="triggerEntries" @onPageChange="triggerPageChange" :dataTable="dataTable" :columns="['token','slug','createdAt','updatedAt','name','strikeCount']">
                        <template slot="column" scope="props">
                            <th>{{props.column}}</th>
                        </template>
                        <template slot="result" scope="props">
                            <tr>
                                <td>{{props.item.getToken()}}</td>
                                <td>{{props.item.getSlug()}}</td>
                                <td>{{props.item.getCreatedAt()}}</td>
                                <td>{{props.item.getUpdatedAt()}}</td>
                                <td>{{props.item.getName()}}</td>
                                <td>{{props.item.getStrikes()}}</td>
                            </tr>
                        </template>
                    </datatable>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    /* @flow */
  import Datatable from './../../../components/Datatable.vue'
  import ShowService from './../../../service/showService'
  export default{
    props: {
    },
    data () {
      return {
        dataTable: null,
        pageChange: 0,
        numEntries: 10
      }
    },
    methods: {
      triggerPageChange: function (value) {
        this.pageChange = value
        this.query()
      },
      triggerEntries: function (value) {
        this.numEntries = value
        this.query()
      },
      query: function () {
        let _this = this
        ShowService.getShowsDatatable(_this.pageChange, [], function (envelope) {
          _this.$set(_this, 'dataTable', envelope.getResult())
        }, function (envelope) {
        }, {
          entries: _this.numEntries
        })
      }
    },
    components: {
      Datatable
    },
    created () {
      this.query()
    }
  }
</script>