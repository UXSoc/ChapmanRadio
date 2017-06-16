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
        showDate: {
          type: String,
          default: null
        }
      },
      methods: {
      },
      computed: {
        formatTime: function () {
          return Moment(this.showDate).format('h:mm a')
        }
      }
    }
</script>