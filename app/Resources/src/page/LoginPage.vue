<template>
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Please Sign In</h3>
            </div>
            <div class="panel-body">
                <form>
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
                    <!--<fieldset>-->
                        <!--<div class="form-group">-->
                            <!--<input class="form-control" placeholder="E-mail" name="email" type="email" autofocus="">-->
                        <!--</div>-->
                        <!--<div class="form-group">-->
                            <!--<input class="form-control" placeholder="Password" name="password" type="password" value="">-->
                        <!--</div>-->
                        <!--<div class="checkbox">-->
                            <!--<label>-->
                                <!--<input name="remember" type="checkbox" value="Remember Me">Remember Me-->
                            <!--</label>-->
                        <!--</div>-->
                        <!--&lt;!&ndash; Change this to a button or input when using this as a form &ndash;&gt;-->
                        <!--<a href="index.html" class="btn btn-lg btn-success btn-block">Login</a>-->
                    <!--</fieldset>-->
                </form>
            </div>
        </div>
    </div>
</template>

<script>
  import {Validator} from 'vee-validate'
  import FormGroup from './../components/FormGroup.vue'
  export default{
    data () {
      return {
        username: '',
        password: '',
        remember_me: false,
        validator: null
      }
    },
    methods: {
      validateForm: function () {
      },
      getParameters: function () {
      },
      login: function () {
        this.validator.validateAll({
          'username': this.username,
          'password': this.password
        }).then(() => {

          let params = new URLSearchParams()
          params.append('_username', this.username)
          params.append('_password', this.password)
          params.append('_remember_me', this.remember_me)

          let temp = this
          axios.post('/login', params).then(function (response) {
            this.$router.push({name: 'index'})
          }).catch(function (error) {
            let e = error.response.data.errors
            console.log(error)
            for(let i = 0; i < e.length; i++)
            {
              temp.errors.add(e[i].field,e[i].message,'auth')
            }
          })
        })
      }
    },
    watch: {
    },
    created() {
      this.validator = new Validator()

      this.validator.attach('username', 'required', { prettyName: 'Username Or Email' })
      this.validator.attach('password', 'required', { prettyName: 'Password' })
    },
    components: {
      FormGroup
    }
  }
</script>