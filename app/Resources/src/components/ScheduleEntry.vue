<template>
    <div class="row schedule-entry">
        <div class="col-md-2">
            <p class="schedule-time">00:00</p>
        </div>
        <div class="col-md-3 trackview">
            <img :src="image">
        </div>
        <div class="col-md-7">
            <p class="schedule-showname">{{show_name}}</p>
            <p class="schedule-showdesc">{{show_excerpt}}</p>
            <p class="schedule-epdesc">{{episode_descrption}}</p>
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
          type: Moment,
          default: null
        },
        image: {
          type: String,
          default: ''
        },
        show_name: {
          type: String,
          default: ''
        },
        show_excerpt: {
          type: String,
          default: ''
        },
        episode_descrption: {
          type: String,
          default: ''
        }
      },
      methods: {
      }
    }
</script>