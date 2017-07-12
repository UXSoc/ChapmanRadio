<template>
    <div>
        <img :src="dj_image">
        <div>
            {{student_name}}
            <br>
            DJ#{{dj_number}}
            #{{student_id}}
        </div>
    </div>
</template>

<script>
  export default{
    data () {
      return {}
    },
    props: {
      dj_image: {
        type: String,
        default: ''
      },
      student_name: {
        type: String,
        default: ''
      },
      dj_number: {
        type: String,
        default: ''
      },
      student_id: {
        type: String,
        default: ''
      }
    },
    methods: {
    }
  }
</script>