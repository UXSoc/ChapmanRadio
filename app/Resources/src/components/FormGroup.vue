<template>
    <div class="form-group" :class="{'has-error': validator.errorBag.has(attribute) }">
        <label class="control-label" :for="name">{{title}}</label>
        <slot></slot>
        <span v-show="validator.errorBag.has(attribute)" class="help-block">{{ validator.errorBag.first(attribute)}}</span>
    </div>

</template>

<script>
  import { Validator } from 'vee-validate'
  export default{
    props: {
      validator: {
        type: Validator,
        default: null
      },
      attribute: {
        type: String,
        default: ''
      },
      name: {
        type: String,
        default: ''
      },
      title: {
        type: String,
        default: ''
      }
    }
  }
</script>