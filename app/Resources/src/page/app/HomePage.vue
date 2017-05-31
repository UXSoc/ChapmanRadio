<template>
    <div>
        <div class="homepage-gradient">
            <play-window></play-window>

            <div class="container">
                <h1 class="cr_header">What's New</h1>
                <div class="whatsnew">
                    <div class="row">
                        <wide-box></wide-box>
                        <div class="col-md-3 wn-smallbox">
                            <img src="./../../../img/apple-laptop.jpg">
                            <div class="wn-smallbox-text">
                                <p class="wn-sb-title">Lorem ipsum dolor sit amet consectetur adispiscing elit</p>
                                <p class="wn-sb-desc">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa </p>
                            </div>
                        </div>
                        <div class="col-md-3 wn-smallbox">
                            <img src="./../../../img/concert.jpeg">
                            <div class="wn-smallbox-text">
                                <p class="wn-sb-title">Lorem ipsum dolor sit amet consectetur adispiscing elit</p>
                                <p class="wn-sb-desc">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <h1 class="cr_header">Latest Tracks</h1>
                <div class="latesttracks">
                    <div class="row trackview">
                        <div class="col-md-2 lt-trackbox">
                            <img src="./../../../img/attention.jpg">
                            <div class="lt-text">
                                <p class="lt-title">Attention</p>
                                <p class="lt-artist">Charlie Puth</p>
                                <p class="lt-minsago">15m ago</p>
                            </div>
                        </div>
                        <div class="col-md-2 lt-trackbox">
                            <img src="./../../../img/adele.jpg">
                            <div class="lt-text">
                                <p class="lt-title">Hello</p>
                                <p class="lt-artist">Adele</p>
                                <p class="lt-minsago">20m ago</p>
                            </div>
                        </div>
                        <div class="col-md-2 lt-trackbox">
                            <img src="./../../../img/heatstroke.jpg">
                            <div class="lt-text">
                                <p class="lt-title">Heatstroke</p>
                                <p class="lt-artist">Calvin Harris</p>
                                <p class="lt-minsago">35m ago</p>
                            </div>
                        </div>
                        <div class="col-md-2 lt-trackbox">
                            <img src="./../../../img/morelife.jpg">
                            <div class="lt-text">
                                <p class="lt-title">Passionfruit</p>
                                <p class="lt-artist">Drake</p>
                                <p class="lt-minsago">40m ago</p>
                            </div>
                        </div>
                        <div class="col-md-2 lt-trackbox">
                            <img src="./../../../img/losemycool.jpg">
                            <div class="lt-text">
                                <p class="lt-title">Lose My Cool</p>
                                <p class="lt-artist">Amber Mark</p>
                                <p class="lt-minsago">45m ago</p>
                            </div>
                        </div>
                        <div class="col-md-2 lt-trackbox">
                            <img src="./../../../img/trackcd.svg">
                            <div class="lt-text">
                                <p class="lt-title">Stitches</p>
                                <p class="lt-artist">Shawn Mendes</p>
                                <p class="lt-minsago">50m ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <h1 class="cr_header">Show of the Week</h1>
                <div class="showoftheweek">
                    <div class="row row-eq-height">
                        <div class="col-md-7 trackview nopadding">
                            <img src="./../../../img/showoftheweek.jpg">
                        </div>
                        <div class="col-md-5 sotw-text nopadding">
                            <p class="sotw-showname">pantherBuck$</p>
                            <p class="sotw-djnames">Courtney Bankhead & Taylor Cox</p>
                            <p class="sotw-desc">Not strictly pop. You will hear everything that has good acoustics, a beat, and vocals. Plus, we're pretty funny so please listen to us!</p>
                            <a class="btn btn-full btncenter" href="#">VIEW EPISODES</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PlayWindow from '../../components/PlayWindow.vue'
    import WideBox from '../../components/WideBox.vue'
    export default{
        data () {
            return {}
        },
        methods: {

        },
        watch: {
        },
        components: {
            PlayWindow,
            WideBox
        }
    }
</script>