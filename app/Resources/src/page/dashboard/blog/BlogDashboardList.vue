<template>
    <div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">Panel heading</div>
                <datatable @onNumOfEntriesChange="triggerEntries" @onPageChange="triggerPageChange" :dataTable="dataTable" :columns="['token','slug','name']">
                    <template slot="column" scope="props">
                        <th>{{props.column}}</th>
                    </template>
                    <template slot="result" scope="props">
                        <tr>
                            <td><router-link :to="props.item.getRouteToEdit ()">{{props.item.token}}</router-link></td>
                            <td><router-link :to="props.item.getRouteToEdit ()">{{props.item.slug}}</router-link></td>
                            <td><router-link :to="props.item.getRouteToEdit ()">{{props.item.name}}</router-link></td>
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
  import PostService from './../../../service/postService'
  export default{
    props: {
    },
    data () {
      return {
        dataTable: null,
        page: 0,
        numEntries: 10
      }
    },
    methods: {
      triggerPageChange: function (value) {
        this.page = value
        this.query()
      },
      triggerEntries: function (value) {
        this.numEntries = value
        this.query()
      },
      query: function () {
        const _this = this
        PostService.getPostsDatatable(_this.page, [], function (datatable) {
          _this.$set(_this, 'dataTable', datatable)
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