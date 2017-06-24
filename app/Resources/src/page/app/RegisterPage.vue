<template>
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Register</h3>
            </div>
            <div class="panel-body">
                <div class="well" v-show="showVerification">Check your email for verification</div>
                <form>
                    <form-group :validator="validator" attribute="username" name="username" title="Username">
                        <input class="form-control" type="text" name="username" v-model="username" id="username" :disabled="showVerification">
                    </form-group>

                    <form-group :validator="validator" attribute="email" name="email" title="Email">
                        <input class="form-control" type="text" name="email" v-model="email" id="email" :disabled="showVerification">
                    </form-group>

                    <form-group :validator="validator" attribute="name" name="name" title="Name">
                        <input class="form-control" type="text" name="name" v-model="name" id="name" :disabled="showVerification">
                    </form-group>

                    <form-group :validator="validator" attribute="studentId" name="studentId" title="Student Id">
                        <input class="form-control" type="text" name="studentId" v-model="studentId" id="studentId" :disabled="showVerification">
                    </form-group>

                    <form-group :validator="validator" attribute="password" name="password" title="Password">
                        <input class="form-control" type="password" name="password" v-model="password" id="password" :disabled="showVerification">
                    </form-group>

                    <form-group :validator="validator" attribute="password_confirmed" name="password_confirmed" title="Repeat Password">
                        <input class="form-control" type="password" name="password_confirmed" v-model="password_confirmed" id="password_confirmed" :disabled="showVerification">
                    </form-group>
                    <button class="btn btn-default" @click="register" type="button" name="button" :disabled="showVerification">Register
                    </button>
                </form>

            </div>
        </div>
    </div>
</template>

<script>
  /* @flow */
  import { Validator } from 'vee-validate'
  import FormGroup from '../../components/FormGroup.vue'
  import axios from 'axios/dist/axios'
  export default{
    data () {
      return {
        username: '',
        email: '',
        name: '',
        password: '',
        password_confirmed: '',
        studentId: '',
        showVerification: false,
        validator: null
      }
    },
    methods: {
      validateForm: function () {
      },
      register: function () {
        let params = new URLSearchParams()
        params.append('name', this.name)
        params.append('username', this.username)
        params.append('password', this.password)
        params.append('studentId', this.studentId)
        params.append('email', this.email)

        this.validator.validateAll({
          name: this.name,
          username: this.username,
          password: this.password,
          studentId: this.studentId,
          email: this.email
        }).then(() => {
          let _this = this
          axios.post(Routing.generate('post_register'), params).then(function (response) {
            _this.showVerification = true
          }).catch(function (error) {
            let e = error.response.data.errors
            for (let i = 0; i < e.length; i++) {
              _this.validator.errorBag.add(e[i].field, e[i].message)
            }
          })
        })
      }
    },
    watch: {
      username: function (val) {
        this.validator.validate('username', val)
      },
      email: function (val) {
        this.validator.validate('email', val)
      },
      name: function (val) {
        this.validator.validate('name', val)
      },
      password: function (val) {
        this.validator.validate('password', val)
      },
      password_confirmed: function (val) {
        this.validator.validate('password', this.password)
      },
      studentId: function (val) {
        this.validator.validate('studentId', val)
      }

    },
    created () {
      this.validator = new Validator()
      this.validator.attach('name', 'required', {prettyName: 'Name'})
      this.validator.attach('username', 'required', {prettyName: 'Username'})
      this.validator.attach('email', 'required|email', {prettyName: 'Email'})
      this.validator.attach('studentId', 'digits:7', {prettyName: 'Student Id'})
      this.validator.attach('password', 'required|confirmed:password_confirmed', {prettyName: 'Password'})
      this.validator.attach('password_confirmed', 'required', {prettyName: 'Confirm Password'})
    },
    components: {
      FormGroup
    }
  }
</script>