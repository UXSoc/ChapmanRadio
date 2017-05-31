<template>
    <div class="play-window">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 radiohead-logo">
                    <img src="./../../img/radiohead-logo.svg">
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4 oa-picture nopadding">
                            <a class="oa-btn oa-btn-full" href="#">Listen live</a>
                        </div>
                        <div class="col-md-8 oa-desc">
                            <div class="oa-timeslot">
                                <div><strong>On air</strong></div>
                                <div class="oa-startendtime">{{ timeslot }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="oa-showname"><strong>{{ show_name }}</strong></div>
                                </div>
                                <div class="col-md-6">
                                    <div>{{ episode_desc }}</div>
                                </div>
                            </div>
                            <div>{{ dj_names }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 oa-nextshow">
                    Next Show
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default{
    data () {
      return {}
    },
    props: {
        show_name: {
            type: String,
            default: 'Planet Moon'
        },
        episode_desc: {
            type: String,
            default: 'Cosmin TRG, Cleric and Sound of Vast!'
        },
        dj_names: {
            type: String,
            default: 'Ted Davis & Jackson Cripe'
        },
        timeslot: {
            type: String,
            default: '22:00 - 23:00'
        }
    },
    methods: {
    }
  }
</script>