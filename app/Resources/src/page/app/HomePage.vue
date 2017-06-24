<template>
    <div>
        <div class="homepage-gradient">
            <play-window show_name="Planet Moon" episode_desc="Cosmin TRG, Cleric and Sound of Vast!" dj_names="Ted Davis & Jackson Cripe" timeslot="22:00 - 23:00"></play-window>

            <div class="container">
                <h2 class="cr_header">What's New</h2>
                <div class="row-resp" v-if="posts">
                    <template v-for="(item, index) in posts">
                        <wide-box v-if="index == 0" :post="item"></wide-box>
                        <small-box v-else :post="item"></small-box>
                    </template>
                </div>
                <div v-else>
                    <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <div class="container">
                <h2 class="cr_header">Latest Tracks</h2>
                <div class="row">
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15" img_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15" img_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15" img_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15" img_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15" img_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></track-box>
                    <track-box class="col-md-2" track_name="Attention" artist="Charlie Puth" mins_ago="15" img_url="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg"></track-box>
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
    /* @flow */
    import PostService from '../../service/postService'
    import Envelope from '../../entity/envelope'
    import Post from '../../entity/post'
    import PlayWindow from '../../components/PlayWindow.vue'
    import WideBox from '../../components/WideBox.vue'
    import SmallBox from '../../components/SmallBox.vue'
    import TrackBox from '../../components/TrackBox.vue'
    import ShowBox from '../../components/ShowBox.vue'
    export default{
      data () {
        return {
          posts: null
        }
      },
      methods: {
        query: function (page) {
          let _this = this
          PostService.getPosts(0, (result: Envelope<Pagination<Post>>) => {
            _this.$set(_this, 'posts', result.getResult().getResult())
          }, (data) => {
          }, {entries: 3})
        }
      },
      watch: {
      },
      created () {
        this.query()
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