<template>
    <div>
        <a></a>
        <h1>Profile</h1>
        <div>
            <div>
                <h2> My Profile</h2>
            </div>
        </div>
        <div>
            <h2> My Password</h2>
            <new-password-form @success="updatePasswordSuccess"></new-password-form>
            <profile-image></profile-image>

        </div>
    </div>
</template>

<script>
    /* @flow */
    import ProfileImage from '../../../form/ProfileImageForm.vue'
    import NewPasswordForm from './../../../form/NewPasswordForm.vue'
    export default {
      data () {
        return {}
      },
      methods: {
        updatePasswordSuccess: function () {
          toastr.success('Account Updated', 'Password Changed', { 'positionClass': 'toast-top-right-offset' })
        }
      },
      components: {
        NewPasswordForm,
        ProfileImage
      }
    }
</script>