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
        </div>
    </div>
</template>

<script>
    import NewPasswordForm from './../../../form/NewPasswordForm.vue'
    export default {
      data () {
        return {}
      },
      components: {
        NewPasswordForm
      }
    }
</script>