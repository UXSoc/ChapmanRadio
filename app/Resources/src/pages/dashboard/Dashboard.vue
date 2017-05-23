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
                        <li><a href="login.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
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
  export default{
    data () {
      return {
        menu: [
          {'name': 'Dashboard', 'route': 'dashboard', 'icon': 'fa-bar-chart-o'},
          {
            'name': 'Shows',
            'icon': 'fa-bar-chart-o',
            'sub_menu': [
              {name: 'All Shows', route: 'dashboard_all_show', 'icon': 'fa-bar-chart-o'},
              {name: 'Add New', route: 'dashboard_add_new_show', 'icon': 'fa-bar-chart-o'},
              {name: 'Categories', route: 'dashboard_category_show', 'icon': 'fa-bar-chart-o'},
              {name: 'Tags', route: 'dashboard_tag_show', 'icon': 'fa-bar-chart-o'}
            ]
          },
          {
            'name': 'Posts',
            'icon': 'fa-bar-chart-o',
            'sub_menu': [
              {name: 'All Posts', route: 'dashboard_all_post', 'icon': 'fa-bar-chart-o'},
              {name: 'Add New', route: 'dashboard_add_new_post', 'icon': 'fa-bar-chart-o'},
              {name: 'Categories', route: 'dashboard_category_post', 'icon': 'fa-bar-chart-o'},
              {name: 'Tags', route: 'dashboard_tag_post', 'icon': 'fa-bar-chart-o'}
            ]
          },
          {'name': 'Comments', 'route': 'dashboard_all_comment', 'icon': 'fa-bar-chart-o'},
          {
            'name': 'Users',
            'icon': 'fa-bar-chart-o',
            'sub_menu': [
              {name: 'All Users', route: 'dashboard_all_user', 'icon': 'fa-bar-chart-o'},
              {name: 'Add New', route: 'dashboard_add_new_user', 'icon': 'fa-bar-chart-o'}
            ]
          },
          {'name': 'Settings', 'route': 'dashboard_setting', 'icon': 'fa-bar-chart-o'}
        ]
      }
    },
    methods: {

    },
    mounted: function () {
      $('#side-menu').metisMenu({toggle: false})

      $(window).bind('load resize', function () {
        var topOffset = 50
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width
        if (width < 768) {
          $('div.navbar-collapse').addClass('collapse')
          topOffset = 100 // 2-row-menu
        } else {
          $('div.navbar-collapse').removeClass('collapse')
        }

        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1
        height = height - topOffset
        if (height < 1) height = 1
        if (height > topOffset) {
          $('#page-wrapper').css('min-height', (height) + 'px')
        }
      })
    },
    watch: {
    },
    components: {
    }
  }
</script>