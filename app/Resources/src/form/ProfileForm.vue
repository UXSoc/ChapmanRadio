<template>
    <form @submit.prevent="update()" >
        <div class="form-group" :class="{'input': true, 'has-error': errors.has('First Name') }">
            <label class="control-label">First Name</label>
            <input class="form-control" type="text" name="First Name"  v-validate="'required'" v-model="value.firstName" id="firstName">
            <span v-show="errors.has('First Name')" class="help-block">{{ errors.first('First Name') }}</span>
        </div>
        <div class="form-group" :class="{'input': true, 'has-error': errors.has('Last Name') }">
            <label class="control-label">Last Name</label>
            <input class="form-control" type="text" name="Last Name"  v-validate="'required'" v-model="value.lastName" id="lastName">
            <span v-show="errors.has('Last Name')" class="help-block">{{ errors.first('Last Name') }}</span>
        </div>
        <button class="btn btn-default"  type="submit" name="button">Update</button>
    </form>

</template>


<script>
    /* @flow */
    import FormGroup from '../components/FormGroup.vue'
    import Profile from '../entity/profile'
    export default {
      props: {
        value: {
          type: Profile,
          default: () => new Profile({})
        }
      },
      data () {
        return {
        }
      },
      methods: {
        update: function () {
          const _this = this
          _this.$validator.validateAll().then(result => {
            _this.$emit('input', _this.value)
          })
        }
      },
      watch: {
      },
      created () {
      },
      components: {
        FormGroup
      }
    }

</script>