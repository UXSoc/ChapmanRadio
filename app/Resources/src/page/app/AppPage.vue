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

    </div>
</template>

<script>
</script>