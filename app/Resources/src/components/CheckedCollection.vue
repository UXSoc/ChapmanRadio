<template>
    <div>
        <div v-for="item in items">
            <input type="checkbox" :value="item" v-model="selected"/>{{item}}
        </div>
    </div>
</template>

<script>
  export default{
    props: {
      selected: {
        type: Array,
        default: () => []
      },
      items: {
        type: Array,
        default: () => []
      }
    },
    methods: {
    },
    data () {
      return {
      }
    }
  }
</script>