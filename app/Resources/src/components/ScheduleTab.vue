<template>
    <router-link class="schedule-tab" :class="{'schedule-tab-today': (isToday | isCurrent)}" :to="{name: 'schedule_list', params: {year: date.format('YYYY'), month:date.format('MMM') ,day:date.format('D')}}" tag="a">
        <template v-if="!isToday">{{date.format('ddd')}}</template>
        <template v-else>Today</template><br>{{date.format('MMM')}} {{date.format('D')}}
    </router-link>
</template>

<script>
  import Moment from 'moment'
  export default{
    data () {
      return {}
    },
    props: {
      date: {
        type: Moment,
        default: Moment.utc()
      },
      today: {
        type: Moment,
        default: Moment.utc()
      }
    },
    computed: {
      isToday: function () {
        return this.today.isSame(this.date, 'day')
      },
      isCurrent: function () {
        if (this.$route.name === 'schedule_list') {
          return Moment().set({
            year: this.$route.params.year,
            month: this.$route.params.month,
            date: this.$route.params.day
          }).isSame(this.date, 'day')
        } else {
          return this.today.isSame(this.date, 'day')
        }
      }
    }
  }

</script>