<template>
    <div class="container">
        <h2 class="cr_header">Events</h2>
        <div class="row">
            <wide-box title="Greg James' Muscochella Highlights" description="Watch Greg's favorite performances from Chapman Radio's Muscochella at Musco Lawn." image_url="/bundles/public/img/dj-wide.jpeg"></wide-box>
            <small-box title="The definitive A-Z of ChapRadio's Muscochella 2017" description="26 important things that happened last weekend at Muscochella." image_url="/bundles/public/img/dj-wide.jpeg"></small-box>
            <small-box title="Muscochella Hype Playlist" description="Here's what went down when our favorite indie artists took to the stage." image_url="/bundles/public/img/dj-wide.jpeg"></small-box>
        </div>
    </div>
</template>

<script>
    import WideBox from '../../components/WideBox.vue'
    import SmallBox from '../../components/SmallBox.vue'
    export default{
        data () {
            return {}
        },
        methods: {

        },
        watch: {
        },
        components: {
            WideBox,
            SmallBox
        }
    }
</script>