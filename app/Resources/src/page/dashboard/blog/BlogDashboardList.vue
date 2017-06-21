<template>
    <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Panel heading</div>
            <datatable :dataTable="dataTable" :columns="['token','slug','name']">
                <template slot="column" scope="props">
                    <th>{{props.column}}</th>
                </template>
                <template slot="result" scope="props">
                    <tr>
                        <td>{{props.item.getToken()}}</td>
                        <td>{{props.item.getSlug()}}</td>
                        <td>{{props.item.getName()}}</td>
                    </tr>
                </template>
            </datatable>
        </div>

</template>

<script>
  import Datatable from './../../../components/Datatable.vue'
  import PostService from './../../../service/postService'
  export default{
    props: {
    },
    data () {
      return {
        dataTable: null
      }
    },
    components: {
      Datatable
    },
    created () {
      let _this = this
      PostService.getPostsDatatable(0,[], function (envelope) {
        _this.$set(_this, 'dataTable', envelope.getResult())
      }, function (envelope) {
      })
    }
  }
</script>