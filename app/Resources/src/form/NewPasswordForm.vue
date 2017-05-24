<template>
    <form>
        <div v-show="dismissSucess" class="alert alert-success " role="alert">
            <button type="button" class="close" @click="dismissSucess = false"><span aria-hidden="true">&times;</span></button>
            Your password has been changed
        </div>

        <div class="form-group" :class="{'has-error': errors.has('oldPassword') }">
            <label class="control-label" for="oldPassword">Old Password</label>
            <input type="password"  name="password" v-model="oldPassword" class="form-control" id="oldPassword" placeholder="Old Password">
            <span v-show="errors.has('oldPassword')" class="help-block">{{ errors.first('oldPassword') }}</span>
        </div>

        <div class="form-group" :class="{'has-error': errors.has('newPassword') }">
            <label for="newPassword">New Password</label>
            <input type="password" name="newPassword" v-model="newPassword" class="form-control" id="newPassword" placeholder="New Password">
            <span v-show="errors.has('newPassword')" class="help-block">{{ errors.first('newPassword') }}</span>
        </div>
        <div class="form-group" :class="{'has-error': errors.has('repeatPassword') }">
            <label for="repeatPassword">Repeat Password</label>
            <input type="password" name="repeatPassword" v-model="repeatPassword" class="form-control" id="repeatPassword" placeholder="Repeat Password">
            <span v-show="errors.has('repeatPassword')" class="help-block">{{ errors.first('repeatPassword')}}</span>
        </div>
        <button  class="btn btn-default" @click="validateForm" type="button" name="button">Update Password</button>
    </form>
</template>

<script>
  import {Validator} from 'vee-validate'
  export default{
    data () {
      return {
        validator: null,
        oldPassword: '',
        newPassword: '',
        repeatPassword: '',
        errors: null,
        dismissSucess: false
      }
    },
    methods: {
      validateForm: function () {
        this.validator.validateAll(this.getParameters()).then(() => {

          let temp = this
          axios.post(Routing.generate('dashboard_ajax_new_password'), this.getParameters()).then(function (response) {
            temp.dismissSucess = true
          }).catch(function (error) {
            let e = error.response.data.errors
            for(var i = 0; i < e.length; i++)
            {
              temp.errors.add(e[i].field,e[i].message,'auth')
            }
          })

        })
      },
      getParameters: function () {
        return {
          oldPassword: this.oldPassword,
          newPassword: this.newPassword,
          repeatPassword: this.repeatPassword
        }
      }
    },
    watch: {
      oldPassword (value) {
        this.validator.validate('oldPassword', value)
      },
      newPassword (value) {
        this.validator.validate('newPassword', value)
      },
      repeatPassword (value) {
        this.validator.validate('repeatPassword', value)
        this.validator.validate('newPassword', this.newPassword)
      }

    },
    created() {
      this.validator = new Validator()

      this.validator.attach('oldPassword', 'required', { prettyName: 'Old Password' })
      this.validator.attach('newPassword', 'confirmed:repeatPassword', { prettyName: 'Password' })
      this.validator.attach('repeatPassword')

      this.$set(this, 'errors', this.validator.errorBag)
    },
    components: {}
  }
</script>