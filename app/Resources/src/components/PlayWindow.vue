<template>
    <div class="play-window">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 onair-left">
                    Radiohead Logo
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4 onair-listenlive">
                            DJ Picture & Listen Live
                        </div>
                        <div class="col-md-8 onair-description">
                            <div class="onair"><strong>On air</strong></div>
                            <div class="onair --onair-times">{{ on_air_times }}</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="showname"><strong>{{ show_name }}</strong></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="showdesc">{{ episode_desc }}</div>
                                </div>
                            </div>
                            <div class="djnames">{{ dj_names }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 onair-right">
                    next show
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
        on_air_times: {
            type: String,
            default: '22:00 - 23:00'
        }
    },
    methods: {
    }
  }
</script>