<template>
    <div class="alert alert-dismissible" :class="alert" role="alert">
        <button type="button" class="close" @click="closeAlert" ><span aria-hidden="true">&times;</span></button>
        {{ message }}
    </div>
</template>

<script>
  export default{
    data () {
      return {}
    },
    props: {
      message: {
        type: String,
        default: ''
      },
      alert: {
        type: String,
        default: ''
      }
    },
    methods: {
      closeAlert: function () {
        this.$emit('close')
      }
    }
  }
</script>