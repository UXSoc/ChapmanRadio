<template>
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Register</h3>
            </div>
            <div class="panel-body">
                <register-form v-on:input="register" :showVerification="showVerification" :form="registerForm"></register-form>

            </div>
        </div>
    </div>
</template>

<script>
  /* @flow */
  import RegisterForm from '../../form/RegisterForm.vue'
  import AuthService from '../../service/authService'
  import Form from '../../entity/form'
  export default{
    components: {
      RegisterForm
    },
    data () {
      return {
        freeze: false,
        registerForm: new Form({})
      }
    },
    methods: {
      register: function (v) {
        const _this = this
        AuthService.register(v, (result: Form) => {
          if (result.code > 0) {
            _this.$set(_this, 'registerForm', result)
          } else {
            this.showVerification = true
          }
        })
      }
    }
  }

</script>