<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">Broadcast Live</h2>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-comments fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge">{{hours}}:{{mins}}{{meridian}}</div>
                                <div>{{dayofweek}}, {{month}} {{dayofmonth}} {{year}}</div>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-left">Timecheck</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-4">
                <div class="panel panel-success">
                    <div class="panel-heading">Broadcast Live</div>
                    <div class="panel-body">
                        <div><strong>Episode Title:</strong></div>
                        <p>{{episode_title}}</p>
                        <div><strong>Episode Description:</strong></div>
                        <p>{{episode_desc}}</p>
                    </div>
                    <div class="panel-heading panel-success centerinparent success-color">
                        <strong>{{broadcast_status_msg}}</strong>
                    </div>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Previous & Next Show
                    </div>
                    <div class="panel-body">
                        <div><strong>Previously at {{prevhour}}:</strong></div>
                        <p>{{prevshowname}} with {{prevshowhost}}</p>
                        <div><strong>Up next at {{afterhour}}:</strong></div>
                        <p>{{nextshowname}} with {{nextshowhost}}</p>
                    </div>
                    <div class="panel-heading panel-success centerinparent info-color">
                        <strong>Tell your listeners to tune into the next show!</strong>
                    </div>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Currently Playing
                    </div>
                    <div class="panel-body">
                        <div><strong>Song Name:</strong></div>
                        <p>{{songname}}</p>
                        <div><strong>Artist Name:</strong></div>
                        <p>{{artistname}}</p>
                        <div><strong>Album Name:</strong></div>
                        <p>{{albumname}}</p>
                        <img class="player-art" src="https://images.genius.com/df91da4c0c20709e276c25f1bb6ff87f.640x640x1.jpg">
                    </div>
                    <div class="panel-heading panel-success centerinparent info-color">
                        <strong>Update album art using the Track Searcher.</strong>
                    </div>
                </div>
                <chat></chat>
            </div>
            <div class="col-lg-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Episode Title & Description
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{showname}}
                            </div>
                            <div class="col-md-6">
                                <select name="session">
                                    <option value="sesh1">Wednesday March 11 2017</option>
                                    <option value="sesh2">Wednesday March 18 2017</option>
                                    <option value="sesh3">Wednesday March 25 2017</option>
                                    <option value="sesh4">Wednesday April 1 2017</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Episode Title:</strong>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control" placeholder="Type episode title here">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Episode Description:</strong>
                                <input class="form-control" placeholder="Type episode description here (required before broadcasting)">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg btn-block">Update all</button>
                </div>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Announcements & PSAs
                    </div>
                    <div class="panel-body">
                        <ul>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                            <li><p>If you are a student org and want to announce your upcoming events, send in a PSA at psa@chapmanradio.com and have your announcement read on air by our DJs.</p></li>
                        </ul>
                    </div>
                    <div class="panel-heading panel-success centerinparent info-color">
                        <strong>Tell your listeners to tune into the next show!</strong>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Track Searcher
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Song Name:</strong></p>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control" placeholder="Type song name here">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Artist Name:</strong></p>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control" placeholder="Type artist name here">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Album Name:</strong></p>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control" placeholder="Type album name here">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-lg btn-block">Search</button>
                        <div class="row">
                            <div class="col-md-3">
                                <img class="player-art" src="https://images.genius.com/df91da4c0c20709e276c25f1bb6ff87f.640x640x1.jpg">
                            </div>
                            <div class="col-md-6">
                                Passionfruit
                                <br>
                                Drake
                                <br>
                                More Life
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-warning">Use this song</button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Chat from './../../../components/Chat.vue'
    /* @flow */
    export default{
      data () {
        return {
          hours: '03',
          mins: '39',
          meridian: 'PM',
          dayofweek: 'Friday',
          month: 'May',
          dayofmonth: '21',
          year: '2017',
          episode_title: 'Seani B sits in for Target',
          episode_desc: 'Diplo & Friends exclusively in the mix with Diplo fresh from his April tour of Africa! Expect tour hours of house music from AfrikaBurn (Burning Man in South Africa)... I here\'s music from MK, Gorgon City, Sante Sansone, Mujava, Rifon, Maxim Sunbeat and more!!',
          broadcast_status_msg: 'You’re all set for your next broadcast.',
          prevhour: '5PM',
          afterhour: '6PM',
          prevshowname: 'pantherbuck$',
          prevshowhost: 'Taylor Cox & Courtney Bankhead',
          nextshowname: 'Beardguy & Friends',
          nextshowhost: 'DJ Beardguy',
          songname: 'Passionfruit',
          artistname: 'Drake',
          albumname: 'More Life',
          showname: 'Planet Moon',
        }
      },
      methods: {

      },
      watch: {
      },
      components: {
        Chat
      }
    }
</script>