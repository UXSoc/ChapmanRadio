<template>
    <div class="row schedule-entry">
        <div class="col-md-2">
            <p class="schedule-time">{{formatTime}}</p>
        </div>
        <div class="col-md-3 trackview">
            <!--<img :src="image">-->
        </div>
        <div class="col-md-7">
            <p class="schedule-showname">{{show.getName()}}</p>
            <p class="schedule-showdesc">derp</p>
            <p class="schedule-epdesc">{{show.getExcerpt()}}</p>
        </div>
    </div>
</template>

<script>
    import Show from '../entity/show'
    import Moment from 'moment'
    export default{
      data () {
        return {}
      },
      props: {
        show: {
          type: Show,
          default: null
        },
        showTime: {
          type: String,
          default: '00:00:00'
        }
      },
      methods: {
      },
      computed: {
        formatTime: function () {
          return Moment(this.showTime, 'HH:mm:ss').format('h:mm a')
        }
      }
    }
</script>