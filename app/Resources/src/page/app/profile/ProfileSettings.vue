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
            <new-password-form></new-password-form>
            <profile-image></profile-image>

        </div>
    </div>
</template>

<script>
    /* @flow */
    import ProfileImage from './../../../form/ProfileImage.vue'
    import NewPasswordForm from './../../../form/NewPasswordForm.vue'
    export default {
      data () {
        return {}
      },
      methods: {
      },
      components: {
        NewPasswordForm,
        ProfileImage
      }
    }
</script>