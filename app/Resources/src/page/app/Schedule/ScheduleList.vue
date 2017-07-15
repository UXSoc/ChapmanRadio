<template>
    <div class="schedule-shows" v-if="scheduleEntries">
        <p class="schedule-time-heading">EARLY</p>
        <div class="row schedule-entry" v-for="(item, index) in scheduleEntries">
            <div class="col-md-2">
                <p class="schedule-time">{{ convertToTime(item.date) }}</p>
            </div>
            <div class="col-md-3 trackview">
                <!--<img :src="image">-->
            </div>
            <div class="col-md-7">
                <p class="schedule-showname">{{item.show.name}}</p>
                <p class="schedule-showdesc">derp</p>
                <p class="schedule-epdesc">{{item.show.excerpt}}</p>
            </div>
        </div>
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
            })
          })
        },
        convertToTime: function (date: string) {
          return Moment.utc(date).format('h:mm a')
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