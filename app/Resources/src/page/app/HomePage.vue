<template>
    <div>
        <div class="homepage-gradient">
            <play-window show_name="Planet Moon" episode_desc="Cosmin TRG, Cleric and Sound of Vast!" dj_names="Ted Davis & Jackson Cripe" timeslot="22:00 - 23:00"></play-window>

            <div class="container">
                <h2 class="cr_header">What's New</h2>
                <div class="whatsnew">
                    <div class="row">
                        <wide-box title="Greg James' Muscochella Highlights" description="Watch Greg's favorite performances from Chapman Radio's Muscochella at Musco Lawn."></wide-box>
                        <small-box title="The definitive A-Z of ChapRadio's Muscochella 2017" description="26 important things that happened last weekend at Muscochella."></small-box>
                        <small-box title="Muscochella Hype Playlist" description="Here's what went down when our favorite indie artists took to the stage."></small-box>
                    </div>
                </div>
            </div>

            <div class="container">
                <h2 class="cr_header">Latest Tracks</h2>
                <div class="row">
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15"></track-box>
                </div>
            </div>

            <div class="container">
                <h2 class="cr_header">Show of the Week</h2>
                <show-box show_name="pantherBuck$" dj_names="Courtney Bankhead & Taylor Cox" show_desc="Not strictly pop. You will hear everything that has good acoustics, a beat, and vocals. Plus, we're pretty funny so please listen to us!"></show-box>
            </div>
        </div>
    </div>
</template>

<script>
    import PlayWindow from '../../components/PlayWindow.vue'
    import WideBox from '../../components/WideBox.vue'
    import SmallBox from '../../components/SmallBox.vue'
    import TrackBox from '../../components/TrackBox.vue'
    import ShowBox from '../../components/ShowBox.vue'
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
            WideBox,
            SmallBox,
            TrackBox,
            ShowBox
        }
    }
</script>