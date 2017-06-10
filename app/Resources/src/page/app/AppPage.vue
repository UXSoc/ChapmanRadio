<template>
    <div>

        <nav class="navbar cr-navbar">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                        <img alt="Brand" src="./../../../public/img/title.svg"/>
                    </a>
                </div>

                <ul class="nav navbar-nav navbar-left login">
                    <router-link active-class="active" :to="{name: 'login'}" :exact="true" tag="li">
                        <a class="nav-link"><i class="fa fa-user" aria-hidden="true"></i> Login</a>
                    </router-link>
                </ul>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <router-link active-class="active" :to="{name: 'home'}" :exact="true" tag="li">
                            <a class="nav-link">Home</a>
                        </router-link>
                        <router-link active-class="active" :to="{name: 'schedule'}" :exact="true" tag="li">
                            <a class="nav-link">Schedule</a>
                        </router-link>
                        <router-link active-class="active" :to="{name: 'show'}" :exact="true" tag="li">
                            <a class="nav-link">Shows</a>
                        </router-link>
                        <router-link active-class="active" :to="{name: 'event'}" :exact="true" tag="li">
                            <a class="nav-link">Events</a>
                        </router-link>
                        <router-link active-class="active" :to="{name: 'post'}" :exact="true" tag="li">
                            <a class="nav-link">Blog</a>
                        </router-link>
                        <router-link active-class="active" :to="{name: 'contact'}" :exact="true" tag="li">
                            <a class="nav-link">Contact</a>
                        </router-link>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <router-view></router-view>

        <!--Expandable Bottom Fixed Player-->
        <div :class="fullscreen">
            <div class="content-container">
                <div class="color-container"></div>
                <div class="img-container" :style="{ backgroundImage: 'url(' + 'https://images.genius.com/df91da4c0c20709e276c25f1bb6ff87f.640x640x1.jpg' + ')' }"></div>
                <div class="blur"></div>
            </div>
            <div class="container-fluid inner nopadding" :class="isinvisible">
                <div class="row heightfix marginfix">
                    <div class="col-md-5 nopadding">
                        <img class="player-art" src="https://images.genius.com/df91da4c0c20709e276c25f1bb6ff87f.640x640x1.jpg">
                        <div class="player-trackinfo">
                            <p class="player-showname">Planet Moon</p>
                            <p class="player-songname">Passionfruit</p>
                            <p class="player-artistname">Drake</p>
                            <a class="btn-player btn-ghost" href="#">VIEW SHOW PAGE</a>
                        </div>
                    </div>
                    <div class="col-md-2 nopadding heightfix centerinparent">
                        <i class="fa fa-play-circle player-btn"></i>
                    </div>
                    <div style="text-align:right;" class="col-md-5 nopadding heightfix">
                        <i class="fa fa-chevron-up player-btn vertalign" @click="expanded = !expanded; hasoverflow()"></i>
                    </div>
                </div>
            </div>
            <div class="sample" :class="notinvisible">
                <div class="container" style="height:100vh;">
                    <div style="text-align:right;position:relative;padding-top:27px;">
                        <i class="fa fa-close player-btn" @click="expanded = !expanded; hasoverflow()"></i>
                    </div>
                    <div class="row" style="height:50%;padding-top:50px;">
                        <div class="col-md-4">
                            <img class="player-art-exp" src="https://images.genius.com/df91da4c0c20709e276c25f1bb6ff87f.640x640x1.jpg">
                        </div>
                        <div class="col-md-8">
                            <div class="player-text">
                                <p style="font-size:24px;color:#fff;">
                                    Planet Moon<br>
                                </p>
                                <p style="font-size:18px;color:#bdbdbd;">
                                    DJ Crispin
                                </p>
                                <p style="padding-top:50px;font-size:36px;color:#fff;">
                                    Passionfruit<br>
                                </p>
                                <p class="player-artistname-exp">
                                    Drake
                                </p>
                            </div>
                            <div class="row" style="padding-top:20px;">
                                <div class="col-md-4">

                                </div>
                                <div class="col-md-4">
                                    <i class="fa fa-play-circle player-btn centerinparent"></i>
                                </div>
                                <div class="col-md-4">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="height:50%;">
                        this is the chat box
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
      data: function () {
        return {
          expanded: false
        }
      },
      computed: {
        fullscreen: function () {
          return {
            'bottom-fixed': !this.expanded,
            expand: this.expanded
          }
        },
        isinvisible: function () {
          return {
            invisible: this.expanded
          }
        },
        notinvisible: function () {
          return {
            invisible: !this.expanded
          }
        },
        hasoverflow: function () {
          if (this.expanded) {
            document.body.classList.add('noscroll')
          } else {
            document.body.classList.remove('noscroll')
          }
        }
      },
      methods: {
        fetchData () {
          console.log(this.$auth.getStatus())
        }
      },
      watch: {
        '$auth.status' : 'fetchData'
      },
      created () {
      },
      components: {
      }
    }
</script>