<template>
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Please Sign In</h3>
            </div>
            <div class="panel-body">
                <form>
                    <alert v-show="showAlert" alert="alert-danger" :message="alert" @close="showAlert = false"></alert>
                    <form-group :validator="validator" attribute="username" name="username" title="Username Or Email">
                        <input class="form-control" type="text" name="username" v-model="username" id="username">
                    </form-group>
                    <form-group :validator="validator" attribute="password" name="password" title="password">
                        <input class="form-control" type="password" name="password" v-model="password" id="password">
                    </form-group>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" v-model="remember_me" type="checkbox" value="">
                            Remember me
                        </label>
                    </div>
                    <button  class="btn btn-default" @click="login" type="button" name="button">Login</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
  /* @flow */
  import {Validator} from 'vee-validate'
  import FormGroup from '../../components/FormGroup.vue'
  import Alert from '../../components/Alert.vue'
  import AuthService from '../../service/authService'
  import Envelope from './../../entity/envelope'

  export default{
    data () {
      return {
        username: '',
        password: '',
        remember_me: false,
        validator: null,
        alert: '',
        showAlert: false
      }
    },
    methods: {
      login: function () {
        this.$auth.refresh()
        this.validator.validateAll({
          'username': this.username,
          'password': this.password
        }).then(() => {
          let _this = this
          AuthService.login((result: Envelope) => {
            _this.$router.push({name: 'home'})
            _this.$auth.refresh()
          }, (error: Envelope) => {
            _this.showAlert = true
            _this.alert = error.getMessage()
          }, this.username, this.password, this.remember_me)
        })
      }
    },
    watch: {
    },
    created () {
      this.validator = new Validator()

      this.validator.attach('username', 'required', { prettyName: 'Username Or Email' })
      this.validator.attach('password', 'required', { prettyName: 'Password' })
    },
    components: {
      FormGroup,
      Alert
    }
  }
</script>