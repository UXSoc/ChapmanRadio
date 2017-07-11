<template>
    <form>
        <form-group :validator="validator" attribute="title" name="title" title="Title">
            <input class="form-control" type="text" name="title" v-model="oldPassword" id="title">
        </form-group>
        <form-group :validator="validator" attribute="caption" name="caption" title="Caption">
            <input class="form-control" type="text" name="caption" v-model="oldPassword" id="caption">
        </form-group>
        <form-group :validator="validator" attribute="altText" name="altText" title="Alt Text">
            <input class="form-control" type="text" name="altText" v-model="oldPassword" id="altText">
        </form-group>
        <form-group :validator="validator" attribute="description" name="description" title="Description">
            <input class="form-control" type="text" name="description" v-model="oldPassword" id="description">
        </form-group>
    </form>
</template>


<script>
  import { Validator } from 'vee-validate'
  import FormGroup from './../components/FormGroup.vue'

  export default{
    props: {
    },
    data () {
      return {
        validator: null,
        image: null
      }
    },
    created () {
      this.validator = new Validator()
    },
    components: {
      FormGroup
    }
  }
</script>