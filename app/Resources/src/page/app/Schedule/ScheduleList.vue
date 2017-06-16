<template>
    <div class="schedule-shows" v-if="scheduleEntries">
        <p class="schedule-time-heading">EARLY</p>
        <schedule-entry v-for="(item, index) in scheduleEntries" :key="index" :show="item.getShow()" :showDate="item.getDate()"></schedule-entry>
     </div>
</template>

<script>
    import ScheduleEntry from '../../../components/ScheduleEntry.vue'
    import ScheduleService from '../../../service/scheduleService'
    import Moment from 'moment'
    export default{
      data () {
        return {
          current: Moment.utc(),
          start: Moment.utc(),
          scheduleEntries: null
        }
      },
      methods: {
        query: function () {
          const _this = this
          ScheduleService.getTodayDate((envelope) => {
            _this.$set(_this, 'current', Moment(envelope.getResult()))
            if (_this.$route.name === 'schedule_list') {
              _this.$set(_this, 'start', Moment().set({
                year: _this.$route.params.year,
                month: _this.$route.params.month,
                date: _this.$route.params.day
              }))
            } else {
              _this.$set(_this, 'start', Moment(envelope.getResult()))
            }
            ScheduleService.getCurrentDateTime(_this.start.year(), (_this.start.month() + 1), _this.start.date(), (envelope) => {
              _this.$set(_this, 'scheduleEntries', envelope.getResult())
            }, (envelope) => {
            })
          }, (envelope) => {
          })
        }
      },
      watch: {
        '$route': 'query'
      },
      created () {
        this.query()
      },
      components: {
        ScheduleEntry
      }
    }
</script>