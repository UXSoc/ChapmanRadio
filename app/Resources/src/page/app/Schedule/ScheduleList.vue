<template>
    <div class="schedule-shows" v-if="scheduleEntries">
        <p class="schedule-time-heading">EARLY</p>
        <schedule-entry v-for="(item, index) in scheduleEntries" :key="index" :show="item.show" :showDate="item.date"></schedule-entry>
     </div>
</template>

<script>
    /* @flow */
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
          ScheduleService.getTodayDate((time) => {
            this.$set(this, 'current', Moment(time))
            if (this.$route.name === 'schedule_list') {
              this.$set(this, 'start', Moment().set({
                year: this.$route.params.year,
                month: this.$route.params.month,
                date: this.$route.params.day
              }))
            } else {
              this.$set(this, 'start', Moment(time))
            }
            ScheduleService.getScheduleByDate(this.start.year(), this.start.month(), this.start.date(), (entries) => {
              this.$set(this, 'scheduleEntries', entries)
            }, (envelope) => {
            })
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