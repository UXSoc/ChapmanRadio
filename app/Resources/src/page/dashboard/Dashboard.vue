<template>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="">ChapmanRadio Dashboard</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><router-link :to="{name: 'dashboard_profile'}" ><i class="fa fa-user fa-fw"></i> User Profile</router-link>
                        </li>
                        <li><router-link :to="{name: 'dashboard_profile'}"><i class="fa fa-gear fa-fw"></i> Settings</router-link>
                        </li>
                        <li class="divider"></li>
                        <li><a href="/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
            </ul>

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul id="side-menu" class="nav">
                        <li v-for="menuItem in menu">
                            <router-link :to=" ('route' in menuItem) ? {name: menuItem.route} : {name: 'dashboard' } "><i  v-bind:class="menuItem.icon" class="fa fa-fw"></i>{{menuItem.name}}<span class="fa arrow" v-if="'sub_menu' in menuItem"></span></router-link>

                            <ul class="nav nav-second-level" v-if="'sub_menu' in menuItem" >
                                <li v-for="item in menuItem.sub_menu"><router-link :to="{name: item.route }"><i v-bind:class="item.icon" class="fa fa-fw"></i>{{ item.name }}</router-link></li>
                            </ul>
                        </li>

                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">
            <router-view></router-view>
            <!--<div class="container-fluid">-->
                <!--<div class="row">-->
                    <!--<div class="col-lg-12">-->
                        <!--<h1 class="page-header">Blank</h1>-->
                    <!--</div>-->
                <!--</div>-->
            <!--</div>-->
        </div>

    </div>
</template>

<script>
    /* @flow */
    import $ from 'jquery'
    export default{
      data () {
        return {
          menu: [

            //          {
            //            'name': 'Users',
            //            'icon': 'fa-bar-chart-o',
            //            'sub_menu': [
            //              {name: 'All Users', route: 'dashboard_all_user', 'icon': 'fa-bar-chart-o'},
            //              {name: 'Add New', route: 'dashboard_add_new_user', 'icon': 'fa-bar-chart-o'}
            //            ]
            //          },
            { 'name': 'Broadcast Live', 'route': 'dashboard_broadcast_live', 'icon': 'fa-bar-chart-o' },
            { 'name': 'My Shows', 'route': 'dashboard_my_shows', 'icon': 'fa-bar-chart-o' },
            { 'name': 'Grades / Attendance', 'route': 'dashboard_grades_attendance', 'icon': 'fa-bar-chart-o' },
            { 'name': 'Settings', 'route': 'dashboard_settings', 'icon': 'fa-bar-chart-o' },
            { 'name': 'Attendance', 'route': 'dashboard_attendance', 'icon': 'fa-bar-chart-o' },
            { 'name': 'Media', 'route': 'dashboard_media', 'icon': 'fa-bar-chart-o' },
            {
              'name': 'Blog', 'icon': 'fa-bar-chart-o',
              'sub_menu': [
                { name: 'All Posts', route: 'dashboard_blog', 'icon': 'fa-bar-chart-o' },
                { name: 'Add New', route: 'dashboard_blog_add', 'icon': 'fa-bar-chart-o' },
                { name: 'Tags', route: 'dashboard_blog_tags', 'icon': 'fa-bar-chart-o' }
              ]
            },
            { 'name': 'Users', 'route': 'dashboard_users', 'icon': 'fa-bar-chart-o' },
            {
              'name': 'Shows', 'route': 'dashboard_shows', 'icon': 'fa-bar-chart-o',
              'sub_menu': [
                { name: 'All Shows', route: 'dashboard_show', 'icon': 'fa-bar-chart-o' },
                { name: 'Add New', route: 'dashboard_show_add', 'icon': 'fa-bar-chart-o' }
              ]
            },
            { 'name': 'Schedule', 'route': 'dashboard_schedule', 'icon': 'fa-bar-chart-o' },
            { 'name': 'Staff', 'route': 'dashboard_staff', 'icon': 'fa-bar-chart-o' },
            { 'name': 'Grade Management', 'route': 'dashboard_grade_mgmt', 'icon': 'fa-bar-chart-o' },
            { 'name': 'Email Alerts', 'route': 'dashboard_emailalerts', 'icon': 'fa-bar-chart-o' },
            { 'name': 'Strikes', 'route': 'dashboard_strikes', 'icon': 'fa-bar-chart-o' }
          ]
        }
      },
      methods: {},
      created () {
      },
      mounted: function () {
        //      $('#side-menu').metisMenu({toggle: false})
        //
        //      $(window).bind('load resize', function () {
        //        var topOffset = 50
        //        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width
        //        if (width < 768) {
        //          $('div.navbar-collapse').addClass('collapse')
        //          topOffset = 100 // 2-row-menu
        //        } else {
        //          $('div.navbar-collapse').removeClass('collapse')
        //        }
        //
        //        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1
        //        height = height - topOffset
        //        if (height < 1) height = 1
        //        if (height > topOffset) {
        //          $('#page-wrapper').css('min-height', (height) + 'px')
        //        }
        //      })
        //      document.body.classList.add('whiteback')
      },
      components: {}
    }

</script>