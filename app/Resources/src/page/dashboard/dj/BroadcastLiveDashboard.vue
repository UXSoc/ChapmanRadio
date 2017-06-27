<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">Broadcast Live</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i>
                        Broadcast
                    </div>
                    <div class="panel-body">
                        <a class="btn btn-full">Start Broadcasting</a>
                        Planet Moon is OFF AIR
                        <br>
                        3:39PM Friday, May 20
                        <br>
                        Previous Show: pantherBuck$ with Taylor Cox & Courtney Bankhead at 5pm
                        <br>
                        Next Show: Beardguy and Friends with DJ Beardguy at 6pm
                        <br>
                        Episode Title: Seani B sits in for Target
                        <input class="form-control" placeholder="Type episode title here">
                        Episode Description: Diplo & Friends exclusively in the mix with Diplo fresh from his April tour of Africa! Expect tour hours of house music from AfrikaBurn (Burning Man in South Africa)... I here's music from MK, Gorgon City, Sante Sansone, Mujava, Rifon, Maxim Sunbeat and more!!
                        <input class="form-control" placeholder="Type episode description here (required before broadcasting)">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i>
                        Currently Playing
                    </div>
                    <div class="panel-body">
                        Song Name: Passionfruit
                        <br>
                        <input class="form-control" placeholder="Type song name here">
                        Artist Name: Drake
                        <br>
                        <input class="form-control" placeholder="Type artist name here">
                        Album Name: More Life
                        <br>
                        <input class="form-control" placeholder="Type album name here">
                    </div>
                    <div class="panel-footer">
                        <button type="button" class="btn btn-primary btn-lg btn-block">Update all</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i>
                        Track Searcher
                    </div>
                    <div class="panel-body">
                        Tracks from API go here
                    </div>
                    <div class="panel-footer">
                        <input class="form-control" placeholder="Type the song, album, or artist here">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="chat-panel panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-comments fa-fw"></i>
                        Chat
                    </div>
                    <div class="panel-body">
                        <ul class="chat">
                            <li class="left clearfix">
                                        <span class="chat-img pull-left">
                                            <img src="http://placehold.it/50/55C1E7/fff" alt="User Avatar" class="img-circle">
                                        </span>
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <strong class="primary-font">
                                            Jack Sparrow
                                        </strong>
                                        <small class="pull-right text-muted">
                                            <i class="fa fa-clock-o fa-fw"></i>
                                            12 mins ago
                                        </small>
                                    </div>
                                    <p>
                                        Lorem ipsum dolor sin amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.
                                    </p>
                                </div>
                            </li>
                        </ul>
                        <ul class="chat">
                            <li class="right clearfix">
                                        <span class="chat-img pull-right">
                                            <img src="http://placehold.it/50/FA6F57/fff" alt="User Avatar" class="img-circle">
                                        </span>
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <small class="text-muted">
                                            <i class="fa fa-clock-o fa-fw"></i>
                                            13 mins ago
                                        </small>
                                        <strong class="pull-right primary-font">
                                            Will Turner
                                        </strong>
                                    </div>
                                    <p>
                                        Lorem ipsum dolor sin amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.
                                    </p>
                                </div>
                            </li>
                        </ul>
                        <ul class="chat">
                            <li class="left clearfix">
                                        <span class="chat-img pull-left">
                                            <img src="http://placehold.it/50/55C1E7/fff" alt="User Avatar" class="img-circle">
                                        </span>
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <strong class="primary-font">
                                            Jack Sparrow
                                        </strong>
                                        <small class="pull-right text-muted">
                                            <i class="fa fa-clock-o fa-fw"></i>
                                            12 mins ago
                                        </small>
                                    </div>
                                    <p>
                                        Lorem ipsum dolor sin amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.
                                    </p>
                                </div>
                            </li>
                        </ul>
                        <ul class="chat">
                            <li class="right clearfix">
                                        <span class="chat-img pull-right">
                                            <img src="http://placehold.it/50/FA6F57/fff" alt="User Avatar" class="img-circle">
                                        </span>
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <small class="text-muted">
                                            <i class="fa fa-clock-o fa-fw"></i>
                                            13 mins ago
                                        </small>
                                        <strong class="pull-right primary-font">
                                            Will Turner
                                        </strong>
                                    </div>
                                    <p>
                                        Lorem ipsum dolor sin amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales.
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-footer">
                        <div class="input-group">
                            <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here...">
                            <span class="input-group-btn">
                                <button class="btn btn-warning btn-sm" id="btn-chat">
                                    Send
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    /* @flow */
    export default{
      data () {
        return {
        }
      },
      methods: {

      },
      watch: {
      },
      components: {
      }
    }
</script>