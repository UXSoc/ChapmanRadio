<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-7">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        My Profile
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                Full Name
                                <br>
                                DJ Name
                                <br>
                                Email
                                <br>
                                Student ID #
                                <br>
                                Phone Number
                                <br>
                                Class or Club
                            </div>
                            <div class="col-md-6">
                                Xavi Ablaza
                                <br>
                                <input class="form-control" placeholder="Type your DJ name here">
                                <br>
                                <input class="form-control" placeholder="ablaz101@mail.chapman.edu">
                                <br>
                                <input class="form-control" placeholder="Type your student ID here (e.g. 2020202)">
                                <br>
                                <input class="form-control" placeholder="Type your phone number (e.g. 71426220221)">
                                <select name="session">
                                    <option value="class">I'm doing the club for fun</option>
                                    <option value="club">I'm enrolled in the class for credit this SP2017.</option>
                                </select>
                            </div>
                        </div>
                    </div>\
                    <a href="#">
                        <div class="panel-heading panel-success centerinparent success-color">
                            <strong>Update information</strong>
                        </div>
                    </a>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Social Media Login
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">Facebook</div>
                            <div class="col-md-6">
                                <a class="btn btn-block btn-social btn-facebook">
                                <i class="fa fa-facebook"></i> Sign in with Facebook</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">Twitter</div>
                            <div class="col-md-6">
                                <a class="btn btn-block btn-social btn-twitter">
                                    <i class="fa fa-twitter"></i> Sign in with Twitter
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                Google+
                            </div>
                            <div class="col-md-6">
                                <a class="btn btn-block btn-social btn-google-plus">
                                    <i class="fa fa-google-plus"></i> Sign in with Google
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        My Passowrd
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                Current Password
                                <br>
                                New Password
                                <br>
                                Repeat new password
                            </div>
                            <div class="col-md-6">
                                <input class="form-control" type="password" placeholder="Type current password">
                                <br>
                                <input class="form-control" type="password" placeholder="Type new password">
                                <br>
                                <input class="form-control" type="password" placeholder="Type new password again">
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-heading panel-success centerinparent success-color">
                            <strong>Change Password</strong>
                        </div>
                    </a>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Profile Picture
                    </div>
                    <div class="panel-body">
                        some kinda image here
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
        return {}
      },
      methods: {},
      watch: {},
      components: {}
    }
</script>