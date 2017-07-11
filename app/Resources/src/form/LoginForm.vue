<template>
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
</template>

<script>
  import { Validator } from 'vee-validate'
  import FormGroup from './../components/FormGroup.vue'
  import Alert from './../components/Alert.vue'
  import AuthService from '../service/authService'

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
          AuthService.login(this.username, this.password, this.rememberMe, (result: Form) => {
            if (result.code > 0) {
              this.showAlert = true
              this.alert = result.message
            } else {
              this.$router.push({ name: 'home' })
              this.$auth.refresh()
            }
          })
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