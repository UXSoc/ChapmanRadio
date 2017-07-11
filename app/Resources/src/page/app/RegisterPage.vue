<template>
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Register</h3>
            </div>
            <div class="panel-body">
                <register-form></register-form>

            </div>
        </div>
    </div>
</template>

<script>
  /* @flow */
  import RegisterForm from '../../form/RegisterForm.vue'
  export default{
    components: {
      RegisterForm
    }
  }

</script>