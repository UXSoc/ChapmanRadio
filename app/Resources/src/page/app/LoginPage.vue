<template>
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Please Sign In</h3>
            </div>
            <div class="panel-body">
                <login-form></login-form>
            </div>
        </div>
    </div>
</template>

<script>
  /* @flow */
  import LoginForm from './../../form/LoginForm.vue'

  export default{
    data () {
      return {}
    },
    components: {
      LoginForm
    }
  }
</script>