<template>
    <form>
        <label v-if="allowImageUpload" class="btn btn-default btn-file file-button">
            Upload <input type="file" v-on:change="uploadImage" />
        </label>
        <form-group :validator="validator" attribute="oldPassword" name="oldPassword" title="Title">
            <input class="form-control" type="password" name="oldPassword" v-model="oldPassword" id="title">
        </form-group>
        <form-group :validator="validator" attribute="oldPassword" name="oldPassword" title="Slug">
            <input class="form-control" type="password" name="oldPassword" v-model="oldPassword" id="slug">
        </form-group>
        <form-group :validator="validator" attribute="oldPassword" name="oldPassword" title="Excerpt">
            <input class="form-control" type="password" name="oldPassword" v-model="oldPassword" id="excerpt">
        </form-group>
        <form-group :validator="validator" attribute="oldPassword" name="oldPassword" title="Caption">
            <input class="form-control" type="password" name="oldPassword" v-model="oldPassword" id="caption">
        </form-group>
        <form-group :validator="validator" attribute="oldPassword" name="oldPassword" title="Alt Text">
            <input class="form-control" type="password" name="oldPassword" v-model="oldPassword" id="altText">
        </form-group>
    </form>
</template>


<script>
  import { Validator } from 'vee-validate'
  import FormGroup from './../components/FormGroup.vue'

  export default{
    props: {
      allowImageUpload: {
        type: Boolean,
        default: false
      }
    },
    data () {
      return {
        validator: null,
        image: null
      }
    },
    methods: {
      uploadImage: function (e) {
        const files = e.target.files || e.dataTransfer.files
        if (!files.length) {
          return
        }
        this.image = files[0]
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