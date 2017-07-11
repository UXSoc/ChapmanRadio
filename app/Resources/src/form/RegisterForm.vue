<template>
    <form>
        <div class="well" v-show="showVerification">Check your email for verification</div>
        <form-group :validator="validator" attribute="username" name="username" title="Username">
            <input class="form-control" type="text" name="username" v-model="username" id="username" :disabled="showVerification">
        </form-group>

        <form-group :validator="validator" attribute="email" name="email" title="Email">
            <input class="form-control" type="text" name="email" v-model="email" id="email" :disabled="showVerification">
        </form-group>

        <form-group :validator="validator" attribute="firstName" name="firstName" title="First Name">
            <input class="form-control" type="text" name="firstName" v-model="firstName" id="firstName" :disabled="showVerification">
        </form-group>

        <form-group :validator="validator" attribute="lastName" name="lastName" title="Last Name">
            <input class="form-control" type="text" name="lastName" v-model="lastName" id="lastName" :disabled="showVerification">
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

</template>


<script>
    /* @flow */
    import { Validator } from 'vee-validate'
    import FormGroup from '../components/FormGroup.vue'
    import AuthService from '../service/authService'
    import Form from '../entity/form'
    export default{
      data () {
        return {
          username: '',
          email: '',
          firstName: '',
          lastName: '',
          password: '',
          password_confirmed: '',
          studentId: '',
          showVerification: false,
          validator: null
        }
      },
      methods: {
        register: function () {
          this.validator.validateAll({
            username: this.username,
            password: this.password,
            studentId: this.studentId,
            email: this.email,
            firstName: this.firstName,
            lastName: this.lastName
          }).then(() => {
            AuthService.register({
              username: this.username,
              plainTextPassword: this.password,
              studentId: this.studentId,
              email: this.email,
              profile: {
                firstName: this.firstName,
                lastName: this.lastName
              }
            }, (result: Form) => {
              if (result.code > 0) {
                result.FillErrobag(this.validator.errorBag, { plainTextPassword: 'password' })
              } else {
                this.showVerification = true
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
        firstName: function (val) {
          this.validator.validate('firstName', val)
        },
        lastName: function (val) {
          this.validator.validate('lastName', val)
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
        this.validator.attach('firstName', 'required', { prettyName: 'First Name' })
        this.validator.attach('lastName', 'required', { prettyName: 'Last Name' })
        this.validator.attach('username', 'required', { prettyName: 'Username' })
        this.validator.attach('email', 'required|email', { prettyName: 'Email' })
        this.validator.attach('studentId', 'digits:7', { prettyName: 'Student Id' })
        this.validator.attach('password', 'required|confirmed:password_confirmed', { prettyName: 'Password' })
        this.validator.attach('password_confirmed', 'required', { prettyName: 'Confirm Password' })
      },
      components: {
        FormGroup
      }
    }

</script>