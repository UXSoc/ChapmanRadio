<template>
    <router-link class="schedule-tab" :class="{'schedule-tab-today': isToday}" :to="{name: 'schedule_list', params: {year: date.format('YYYY'), month:date.format('MMM') ,day:date.format('D')}}" tag="a">
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
      to: {
        type: Object,
        default: null
      },
      today: {
        type: Moment,
        default: Moment.utc()
      }
    },
    computed: {
      isToday: function () {
        return this.today.isSame(this.date, 'day')
      }
    }
  }

</script>