<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">My Shows</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                         Basic Tabs
                    </div>
                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#" data-toggle="tab" aria-expanded="true">Show 1</a>
                            </li>
                            <li>
                                <a href="#" data-toggle="tab" aria-expanded="false">Show 2</a>
                            </li>
                            <li>
                                <a href="#" data-toggle="tab" aria-expanded="false">Show 3</a>
                            </li>
                            <li>
                                <a href="#" data-toggle="tab" aria-expanded="false">Show 4</a>
                            </li>
                            <li>
                                <a href="#" data-toggle="tab" aria-expanded="false">Show 5</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="show1">
                                <h4>Show 1</h4>
                                <p>Some lorem ipsum son dolor text adispicing elite sed do eisumod tempor indicudnt dal.</p>
                            </div>
                            <div class="tab-pane fade" id="show2">
                                <h4>Show 2</h4>
                                <p>Some lorem ipsum son dolor text adispicing elite sed do eisumod tempor indicudnt dal.</p>
                            </div>
                            <div class="tab-pane fade" id="show3">
                                <h4>Show 3</h4>
                                <p>Some lorem ipsum son dolor text adispicing elite sed do eisumod tempor indicudnt dal.</p>
                            </div>
                            <div class="tab-pane fade" id="show4">
                                <h4>Show 4</h4>
                                <p>Some lorem ipsum son dolor text adispicing elite sed do eisumod tempor indicudnt dal.</p>
                            </div>
                            <div class="tab-pane fade" id="show5">
                                <h4>Show 5</h4>
                                <p>Some lorem ipsum son dolor text adispicing elite sed do eisumod tempor indicudnt dal.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    /* @flow */
    export default{
      data () {
        return {
        }
      },
      methods: {

      },
      watch: {
      },
      components: {
      }
    }
</script>