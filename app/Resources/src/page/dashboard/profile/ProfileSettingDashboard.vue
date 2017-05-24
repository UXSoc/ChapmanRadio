<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            Change Password
        </div>
        <div class="panel-body">
            <new-password-form></new-password-form>
        </div>

    </div>


</template>

<script>
    import NewPasswordForm from '../../../form/NewPasswordForm.vue'
  export default{
    data () {
      return {
      }
    },
    methods: {},
    watch: {},
    components: {
      NewPasswordForm
    }
  }
</script>