<template>
    <div class="schedule-shows">
        <p class="schedule-time-heading">EARLY</p>
        <template v-if="schedule">

        </template>
        <schedule-entry image="/bundles/public/img/showoftheweek.jpg" show_name="Knife Murderers" show_excerpt="DJ Rexford plays you the hottest EDM records to end the night." episode_descrption="Episode special description lorem ipsium dolor sin amet consecutur alit sed ut mank alderstan boniaop visde fallson."></schedule-entry>
        <schedule-entry image="/bundles/public/img/showoftheweek.jpg" show_name="Knife Murderers" show_excerpt="DJ Rexford plays you the hottest EDM records to end the night." episode_descrption="Episode special description lorem ipsium dolor sin amet consecutur alit sed ut mank alderstan boniaop visde fallson."></schedule-entry>
        <schedule-entry image="/bundles/public/img/showoftheweek.jpg" show_name="Knife Murderers" show_excerpt="DJ Rexford plays you the hottest EDM records to end the night." episode_descrption="Episode special description lorem ipsium dolor sin amet consecutur alit sed ut mank alderstan boniaop visde fallson."></schedule-entry>
        <schedule-entry image="/bundles/public/img/showoftheweek.jpg" show_name="Knife Murderers" show_excerpt="DJ Rexford plays you the hottest EDM records to end the night." episode_descrption="Episode special description lorem ipsium dolor sin amet consecutur alit sed ut mank alderstan boniaop visde fallson."></schedule-entry>
        <schedule-entry image="/bundles/public/img/showoftheweek.jpg" show_name="Knife Murderers" show_excerpt="DJ Rexford plays you the hottest EDM records to end the night." episode_descrption="Episode special description lorem ipsium dolor sin amet consecutur alit sed ut mank alderstan boniaop visde fallson."></schedule-entry>
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
          schedule: null
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
            ScheduleService.getCurrentDateTime(_this.start.year(), _this.start.month(), _this.start.date(), (envelope) => {
              _this.$set(_this, 'schedule', envelope.getResult())
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