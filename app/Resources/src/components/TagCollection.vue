<template>
    <div class="tag-collection">
        <span v-for="tag in tags" class="tag">tag <a v-on:click.prevent="removeTag(tag)">x</a></span>
        <input type="text" v-model="tagEntry" v-on:keyup.enter="addTag()"/>
    </div>
</template>

<script>
  export default{
    props: {
      tags: {
        type: Array,
        default: () => []
      }
    },
    data () {
      return {
        tagEntry: ''
      }
    },
    methods: {
      removeTag (tag: string) {
        this.$emit('onTagRemoved', tag)
      },
      addTag () {
        if (this.tagEntry !== '') {
          this.$emit('onTagAdded', this.tagEntry)
        }
        this.tagEntry = ''
      }
    }
  }
</script>