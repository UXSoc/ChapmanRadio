<template>
    <div>

        <nav class="navbar cr-navbar">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle -collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                        <img alt="Brand" src="/bundles/public/img/title.svg"/>
                    </a>
                </div>

                <ul class="nav navbar-nav navbar-left login">
                    <router-link v-if="!status.isLoggedIn()" active-class="active" :to="{name: 'login'}" :exact="true" tag="li">
                        <a class="nav-link"><i class="fa fa-user" aria-hidden="true"></i> Login</a>
                    </router-link>
                    <router-link v-else active-class="active" :to="{name: 'profile'}" :exact="true" tag="li">
                        <a class="nav-link"><i class="fa fa-user" aria-hidden="true"></i> {{status.username}}</a>
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
        <div class="music-player" :class="togglePlayerSize">

            <!--Blur Effect-->
            <div class="blur-container">
                <div class="solidcolor"></div>
                <div class="playerart" :style="{ backgroundImage: 'url(' + 'https://images.genius.com/df91da4c0c20709e276c25f1bb6ff87f.640x640x1.jpg' + ')' }"></div>
                <div class="blureffect"></div>
            </div>

            <!--Collapsed Player Content-->
            <div class="collapsed-player container-fluid inner nopadding" :class="isPlayerExpanded">
                <div class="row heightfix marginfix">
                    <div class="col-md-5 nopadding">
                        <img class="player-art" src="https://images.genius.com/df91da4c0c20709e276c25f1bb6ff87f.640x640x1.jpg">
                        <div class="trackinfo">
                            <p class="showname">Planet Moon</p>
                            <p class="songname">Passionfruit</p>
                            <p class="artistname">Drake</p>
                            <a class="btn-player btn-ghost" href="#">VIEW SHOW PAGE</a>
                        </div>
                    </div>
                    <div class="col-md-2 nopadding heightfix centerinparent">
                        <i class="fa fa-play-circle player-btn"></i>
                    </div>
                    <div style="text-align:right;" class="col-md-5 nopadding heightfix">
                        <i class="fa fa-chevron-up player-btn vertalign" @click="expanded = !expanded; hasOverflow()"></i>
                    </div>
                </div>
            </div>

            <!--Expanded Fullscreen Player Content-->
            <div class="expanded-player" :class="isPlayerCollapsed">
                <div class="container" style="height:100vh;">
                    <div style="text-align:right;position:relative;padding-top:27px;">
                        <i class="fa fa-close player-btn" @click="expanded = !expanded; hasOverflow()"></i>
                    </div>
                    <div class="row" style="height:50%;padding-top:50px;">
                        <div class="col-md-4">
                            <img class="art" src="https://images.genius.com/df91da4c0c20709e276c25f1bb6ff87f.640x640x1.jpg">
                        </div>
                        <div class="col-md-8">
                            <div class="player-text">
                                <p class="showname">
                                    Planet Moon<br>
                                </p>
                                <p class="djname">
                                    DJ Crispin
                                </p>
                                <p class="songname">
                                    Passionfruit<br>
                                </p>
                                <p class="artistname">
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
                        <chat></chat>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    /* @flow */
    import User from './../../entity/user'
    import Chat from './../../components/Chat.vue'
    export default {
      data: function () {
        return {
          expanded: false,
          isLoggedIn: false,
          status: new User({})
        }
      },
      computed: {
        togglePlayerSize: function () {
          return {
            '-collapsed': !this.expanded,
            '-expanded': this.expanded
          }
        },
        isPlayerExpanded: function () {
          return {
            invisible: this.expanded
          }
        },
        isPlayerCollapsed: function () {
          return {
            invisible: !this.expanded
          }
        },
        hasOverflow: function () {
          if (this.expanded) {
            document.body.classList.add('noscroll')
          } else {
            document.body.classList.remove('noscroll')
          }
        }
      },
      methods: {
        userStatus () {
          this.$set(this, 'status', this.$auth.getStatus())
        }
      },
      watch: {
        '$auth.status' : 'userStatus'
      },
      created () {
        this.userStatus()
      },
      components: {
        Chat
      }
    }
</script>