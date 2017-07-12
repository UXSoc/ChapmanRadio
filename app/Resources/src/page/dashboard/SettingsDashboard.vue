<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-7">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        My Password
                    </div>
                    <div class="panel-body">
                        <new-password-form @success="updatePasswordSuccess"></new-password-form>
                        <!--<div class="row">-->
                        <!--<div class="col-md-6">-->
                        <!--Current Password-->
                        <!--<br>-->
                        <!--New Password-->
                        <!--<br>-->
                        <!--Repeat new password-->
                        <!--</div>-->
                        <!--<div class="col-md-6">-->
                        <!--<input class="form-control" type="password" placeholder="Type current password">-->
                        <!--<br>-->
                        <!--<input class="form-control" type="password" placeholder="Type new password">-->
                        <!--<br>-->
                        <!--<input class="form-control" type="password" placeholder="Type new password again">-->
                        <!--</div>-->
                        <!--</div>-->
                    </div>
                    <!--<a href="#">-->
                    <!--<div class="panel-heading panel-success centerinparent success-color">-->
                    <!--<strong>Change Password</strong>-->
                    <!--</div>-->
                    <!--</a>-->
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Profile
                    </div>
                    <div class="panel-body">
                        <profile-form v-on:input="updateProfile" v-model="profile"></profile-form>
                    </div>
                </div>

                <!--<div class="panel panel-default">-->
                    <!--<div class="panel-heading">-->
                        <!--My Profile-->
                    <!--</div>-->
                    <!--<div class="panel-body">-->
                        <!--<div class="row">-->
                            <!--<div class="col-md-6">-->
                                <!--Full Name-->
                                <!--<br>-->
                                <!--DJ Name-->
                                <!--<br>-->
                                <!--Email-->
                                <!--<br>-->
                                <!--Student ID #-->
                                <!--<br>-->
                                <!--Phone Number-->
                                <!--<br>-->
                                <!--Class or Club-->
                            <!--</div>-->
                            <!--<div class="col-md-6">-->
                                <!--Xavi Ablaza-->
                                <!--<br>-->
                                <!--<input class="form-control" placeholder="Type your DJ name here">-->
                                <!--<br>-->
                                <!--<input class="form-control" placeholder="ablaz101@mail.chapman.edu">-->
                                <!--<br>-->
                                <!--<input class="form-control" placeholder="Type your student ID here (e.g. 2020202)">-->
                                <!--<br>-->
                                <!--<input class="form-control" placeholder="Type your phone number (e.g. 71426220221)">-->
                                <!--<select name="session">-->
                                    <!--<option value="class">I'm doing the club for fun</option>-->
                                    <!--<option value="club">I'm enrolled in the class for credit this SP2017.</option>-->
                                <!--</select>-->
                            <!--</div>-->
                        <!--</div>-->
                    <!--</div>\-->
                    <!--<a href="#">-->
                        <!--<div class="panel-heading panel-success centerinparent success-color">-->
                            <!--<strong>Update information</strong>-->
                        <!--</div>-->
                    <!--</a>-->
                <!--</div>-->

                <!--<div class="panel panel-default">-->
                    <!--<div class="panel-heading">-->
                        <!--Social Media Login-->
                    <!--</div>-->
                    <!--<div class="panel-body">-->
                        <!--<div class="row">-->
                            <!--<div class="col-md-6">Facebook</div>-->
                            <!--<div class="col-md-6">-->
                                <!--<a class="btn btn-block btn-social btn-facebook">-->
                                <!--<i class="fa fa-facebook"></i> Sign in with Facebook</a>-->
                            <!--</div>-->
                        <!--</div>-->
                        <!--<div class="row">-->
                            <!--<div class="col-md-6">Twitter</div>-->
                            <!--<div class="col-md-6">-->
                                <!--<a class="btn btn-block btn-social btn-twitter">-->
                                    <!--<i class="fa fa-twitter"></i> Sign in with Twitter-->
                                <!--</a>-->
                            <!--</div>-->
                        <!--</div>-->
                        <!--<div class="row">-->
                            <!--<div class="col-md-6">-->
                                <!--Google+-->
                            <!--</div>-->
                            <!--<div class="col-md-6">-->
                                <!--<a class="btn btn-block btn-social btn-google-plus">-->
                                    <!--<i class="fa fa-google-plus"></i> Sign in with Google-->
                                <!--</a>-->
                            <!--</div>-->
                        <!--</div>-->
                    <!--</div>-->
                <!--</div>-->
            </div>
            <div class="col-lg-5">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        Profile Picture
                    </div>
                    <div class="panel-body">
                        <profile-image-form></profile-image-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    /* @flow */
    /* global toastr */
    import Profile from '../../entity/profile'
    import Form from '../../entity/form'
    import NewPasswordForm from '../../form/NewPasswordForm.vue'
    import ProfileImageForm from '../../form/ProfileImageForm.vue'
    import ProfileForm from '../../form/ProfileForm.vue'
    import AccountService from '../../service/accountService'
    export default{
      data () {
        return {
          profile: new Profile({})
        }
      },
      methods: {
        updateProfile: function (val) {
          const _this = this
          AccountService.patchProfile(val, (c) => {
            if (c instanceof Profile) {
              _this.$set(_this, 'profile', c)
              toastr.success('Profile Updated', '', { 'positionClass': 'toast-top-right' })
            } else if (c instanceof Form) {
            }
          })
        },
        updatePasswordSuccess: function () {
          toastr.success('Account Updated', 'Password Changed', { 'positionClass': 'toast-top-right' })
        }
      },
      watch: {},
      created () {
        const _this = this
        AccountService.getProfile((c) => {
          _this.$set(_this, 'profile', c)
        })
      },
      components: {
        NewPasswordForm,
        ProfileImageForm,
        ProfileForm
      }
    }
</script>