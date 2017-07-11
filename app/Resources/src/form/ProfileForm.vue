<template>
    <form>
        <form-group :validator="validator" attribute="firstName" name="firstName" title="First Name">
            <input class="form-control" type="text" name="firstName" v-model="firstName" id="firstName" :disabled="showVerification">
        </form-group>
        <form-group :validator="validator" attribute="lastName" name="lastName" title="Last Name">
            <input class="form-control" type="text" name="lastName" v-model="lastName" id="lastName" :disabled="showVerification">
        </form-group>
        <button class="btn btn-default" @click="register" type="button" name="button" :disabled="showVerification">Update</button>
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
          firstName: '',
          lastName: '',
          validator: null
        }
      },
      methods: {
        register: function () {
          this.validator.validateAll({
            firstName: this.firstName,
            lastName: this.lastName
          }).then(() => {
            AuthService.register({
              firstName: this.firstName,
              lastName: this.lastName
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
        firstName: function (val) {
          this.validator.validate('firstName', val)
        },
        lastName: function (val) {
          this.validator.validate('lastName', val)
        }

      },
      created () {
        this.validator = new Validator()
        this.validator.attach('firstName', 'required', { prettyName: 'First Name' })
        this.validator.attach('lastName', 'required', { prettyName: 'Last Name' })
      },
      components: {
        FormGroup
      }
    }

</script>