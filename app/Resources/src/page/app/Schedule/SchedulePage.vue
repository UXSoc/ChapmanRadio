<template>
    <div class="schedule-bkgrnd">
        <div class="container">
            <h1 class="cr_header">Schedule</h1>
            <div class="row">
                <div class="col-md-12 nopadding">
                    <p class="bold-date">Tuesday May 23 2017</p>
                </div>
            </div>
            <div class="row">
                <div class="container schedule-bar">
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        TUE<br>MAY 16
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        WED<br>MAY 17
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        THURS<br>MAY 18
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        FRI<br>MAY 19
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        SAT<br>MAY 20
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        SUN<br>MAY 21
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        MON<br>MAY 22
                    </router-link>
                    <router-link class="schedule-tab-today" :to="{name: 'post'}" tag="a">
                        TODAY<br>MAY 23
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        WED<br>MAY 24
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        THU<br>MAY 25
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        FRI<br>MAY 26
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        SAT<br>MAY 27
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        SUN<br>MAY 28
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        MON<br>MAY 29
                    </router-link>
                    <router-link class="schedule-tab" :to="{name: 'post'}" tag="a">
                        TUE<br>MAY 30
                    </router-link>
                </div>
            </div>
            <div class="row">
                <router-view></router-view>
            </div>

            <div class="row">
                <div class="col-md-12 nopadding schedule-nextday">
                    <p class="nextday">Wednesday May 24 2017 ></p>
                </div>
            </div>
        </div>


        <div class="container schedule-shows">


        </div>
    </div>
</template>

<script>
    import ScheduleEntry from '../../../components/ScheduleEntry.vue'
    export default{
      data () {
        return {}
      },
      methods: {},
      watch: {},
      components: {
        ScheduleEntry
      }
    }
</script>