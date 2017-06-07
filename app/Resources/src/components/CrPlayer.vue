<template>
    <div class="bottom-fixed">
        <div class="content-container">
            <div class="color-container"></div>
            <div class="img-container" :style="{ backgroundImage: 'url(' + image + ')' }"></div>
            <div class="blur"></div>
        </div>
        <div class="container-fluid inner nopadding">
            <div class="row heightfix marginfix">
                <div class="col-md-5 nopadding">
                    <img class="player-art" :src="image">
                    <div class="player-trackinfo">
                        <p class="player-showname">Planet Moon</p>
                        <p class="player-songname">Passionfruit</p>
                        <p class="player-artistname">Drake</p>
                        <a class="btn-player btn-ghost" href="#">VIEW SHOW PAGE</a>
                    </div>
                </div>
                <div class="col-md-2 nopadding heightfix centerinparent">
                    <i class="fa fa-play-circle player-btn"></i>
                </div>
                <div style="text-align:right;" class="col-md-5 nopadding heightfix">
                    <i class="fa fa-chevron-up player-btn vertalign"></i>
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
            image: {
                type: String,
                default: ''
            }
        },
        methods: {
        }
    }
</script>