<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">Users</h2>
            </div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Find User
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input class="form-control" placeholder="Type DJ number, student ID, name or email">
                            <button type="button" class="btn btn-success">Success</button>
                        </div>
                        <div class="col-md-6">
                            Filter by:
                            <select name="session">
                                <option value="genre1">2017SP - Spring</option>
                                <option value="genre2">2018F - Fall</option>
                                <option value="genre3">2017SP - Spring</option>
                                <option value="genre4">2017SP - Spring</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                                <thead>
                                <tr role="row"><th class="sorting_asc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 184px;">Rendering engine</th><th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 226px;">Browser</th><th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 209px;">Platform(s)</th><th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 159px;">Engine version</th><th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 123px;">CSS grade</th></tr>
                                </thead>
                                <tbody>
                                <tr class="gradeA odd" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Firefox 1.0</td>
                                    <td>Win 98+ / OSX.2+</td>
                                    <td class="center">1.7</td>
                                    <td class="center">A</td>
                                </tr><tr class="gradeA even" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Firefox 1.5</td>
                                    <td>Win 98+ / OSX.2+</td>
                                    <td class="center">1.8</td>
                                    <td class="center">A</td>
                                </tr><tr class="gradeA odd" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Firefox 2.0</td>
                                    <td>Win 98+ / OSX.2+</td>
                                    <td class="center">1.8</td>
                                    <td class="center">A</td>
                                </tr><tr class="gradeA even" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Firefox 3.0</td>
                                    <td>Win 2k+ / OSX.3+</td>
                                    <td class="center">1.9</td>
                                    <td class="center">A</td>
                                </tr><tr class="gradeA odd" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Camino 1.0</td>
                                    <td>OSX.2+</td>
                                    <td class="center">1.8</td>
                                    <td class="center">A</td>
                                </tr><tr class="gradeA even" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Camino 1.5</td>
                                    <td>OSX.3+</td>
                                    <td class="center">1.8</td>
                                    <td class="center">A</td>
                                </tr><tr class="gradeA odd" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Netscape 7.2</td>
                                    <td>Win 95+ / Mac OS 8.6-9.2</td>
                                    <td class="center">1.7</td>
                                    <td class="center">A</td>
                                </tr><tr class="gradeA even" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Netscape Browser 8</td>
                                    <td>Win 98SE+</td>
                                    <td class="center">1.7</td>
                                    <td class="center">A</td>
                                </tr><tr class="gradeA odd" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Netscape Navigator 9</td>
                                    <td>Win 98+ / OSX.2+</td>
                                    <td class="center">1.8</td>
                                    <td class="center">A</td>
                                </tr><tr class="gradeA even" role="row">
                                    <td class="sorting_1">Gecko</td>
                                    <td>Mozilla 1.0</td>
                                    <td>Win 95+ / OSX.1+</td>
                                    <td class="center">1</td>
                                    <td class="center">A</td>
                                </tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Fetch Emails
                </div>
                <div class="panel-body">
                    <select name="session">
                        <option value="genre1">2017SP - Spring</option>
                        <option value="genre2">2018F - Fall</option>
                        <option value="genre3">2017SP - Spring</option>
                        <option value="genre4">2017SP - Spring</option>
                    </select>
                    <select name="group">
                        <option value="classclub">Class + Club</option>
                        <option value="class">Class</option>
                        <option value="club">Club</option>
                    </select>
                    <input class="form-control" placeholder="Emails in standard format will be here">
                    <input class="form-control" placeholder="Emails in MailChimp format will be here">
                </div>
                <div class="panel-heading panel-success">
                    Fetch
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