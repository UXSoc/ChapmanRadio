<template>
    <form @submit.prevent="register()" >
        <div class="well" v-show="showVerification">Check your email for verification</div>

        <div class="form-group" :class="{'input': true, 'has-error': errors.has('Username') }">
            <label class="control-label" >Username</label>
            <input class="form-control" type="text" name="Username"  v-validate="'required'" v-model="value.username" id="username"  :disabled="showVerification">
            <span v-show="errors.has('Username')" class="help-block">{{ errors.first('Username') }}</span>
        </div>

        <div class="form-group" :class="{'input': true, 'has-error': errors.has('Email') }">
            <label class="control-label" >Email</label>
            <input class="form-control" type="text" name="Email"  v-validate="'required|email'" v-model="value.email" id="email"  :disabled="showVerification">
            <span v-show="errors.has('Email')" class="help-block">{{ errors.first('Email') }}</span>
        </div>

        <div class="form-group" :class="{'input': true, 'has-error': errors.has('First Name') }">
            <label class="control-label">First Name</label>
            <input class="form-control" type="text" name="First Name"  v-validate="'required'" v-model="value.profile.firstName" id="firstName"  :disabled="showVerification">
            <span v-show="errors.has('First Name')" class="help-block">{{ errors.first('First Name') }}</span>
        </div>

        <div class="form-group" :class="{'input': true, 'has-error': errors.has('Last Name') }">
            <label class="control-label" >Last Name</label>
            <input class="form-control" type="text" name="Last Name"  v-validate="'required'" v-model="value.profile.lastName" id="lastName"  :disabled="showVerification">
            <span v-show="errors.has('Last Name')" class="help-block">{{ errors.first('Last Name') }}</span>
        </div>

        <div class="form-group" :class="{'input': true, 'has-error': errors.has('Student Id') }">
            <label class="control-label" >Student Id</label>
            <input class="form-control" type="text" name="Student Id"  v-validate="'digits:7'" v-model="value.studentId" id="studentId"  :disabled="showVerification">
            <span v-show="errors.has('Student Id')" class="help-block">{{ errors.first('Student Id') }}</span>
        </div>

        <div class="form-group" :class="{'input': true, 'has-error': errors.has('Password') }">
            <label class="control-label" >Password</label>
            <input class="form-control" type="password" name="Password"  v-validate="'required|confirmed:Password Confirmed'" v-model="value.password" id="password"  :disabled="showVerification">
            <span v-show="errors.has('Password')" class="help-block">{{ errors.first('Password') }}</span>
        </div>

        <div class="form-group" :class="{'input': true, 'has-error': errors.has('Password Confirmed') }">
            <label class="control-label" for="firstName">Password Confirmed</label>
            <input class="form-control" type="password" name="Password Confirmed"  v-validate="'required'" id="password_confirmed"  :disabled="showVerification">
            <span v-show="errors.has('Password Confirmed')" class="help-block">{{ errors.first('Password Confirmed') }}</span>
        </div>


        <button class="btn btn-default" type="submit" name="button" :disabled="showVerification">Register</button>
    </form>

</template>


<script>
    /* @flow */
    import User from '../entity/user'
    import Form from '../entity/form'
    export default{
      props: {
        value: {
          type: User,
          default: () => new User({})
        },
        showVerification: {
          type: Boolean,
          default: false
        },
        form: {
          type: Form,
          default: () => new Form({})
        }
      },
      data () {
        return {
        }
      },
      watch: {
        'form': function () {
          this.form.fillErrorbag(this.errors, {
            'email': 'Email',
            'username': 'Username'
          })
        }
      },
      methods: {
        register: function () {
          const _this = this
          _this.$validator.validateAll().then(result => {
            _this.$emit('input', _this.value)
          })
        }
      }
    }

</script>