<template>
    <div class="schedule-bkgrnd">
        <div class="container">
            <h1 class="cr_header">Schedule</h1>
            <div class="row">
                <div class="col-md-12 nopadding">
                    <p class="bold-date">{{start.format("dddd, MMMM Do YYYY")}}</p>
                </div>
            </div>
            <div class="row">
                <div class="container schedule-bar">
                    <schedule-tab :date="start.clone().subtract(8, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().subtract(7, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().subtract(6, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().subtract(5, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().subtract(4, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().subtract(3, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().subtract(2, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().subtract(1, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone()" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().add(1, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().add(2, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().add(3, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().add(4, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().add(5, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().add(6, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().add(7, 'days')" :today="current"></schedule-tab>
                    <schedule-tab :date="start.clone().add(8, 'days')" :today="current"></schedule-tab>
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
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import ScheduleEntry from '../../../components/ScheduleEntry.vue'
    import ScheduleService from '../../../service/scheduleService'
    import Moment from 'moment'
    import ScheduleTab from '../../../components/ScheduleTab.vue'
    export default{
      data () {
        return {
          current: Moment.utc(),
          start: Moment.utc()
        }
      },
      methods: {
        updateSchedule: function () {
          const _this = this
          ScheduleService.getTodayDate((envelope) => {
            _this.$set(_this, 'current', Moment(envelope.getResult()))
            if (_this.$route.name === 'schedule_list') {
              _this.$set(_this, 'start', Moment().set({year: _this.$route.params.year, month: _this.$route.params.month, date: _this.$route.params.day}))
            } else {
              _this.$set(_this, 'start', Moment(envelope.getResult()))
            }
          }, (envelope) => {
          })
        }
      },
      watch: {
        '$route': 'updateSchedule'
      },
      created () {
        this.updateSchedule()
      },
      components: {
        ScheduleEntry,
        ScheduleTab
      }
    }
</script>