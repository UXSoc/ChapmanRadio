<template>
    <div v-show="visible">
        <div>
            <div ref="editor"></div>
        </div>
        <button v-on:click.prevent="submit()">Submit</button>
    </div>
</template>

<script>
    import Quill from '../../quill/quill'
    import { fromDelta, toDelta } from 'quill-delta-markdown'

    export default{
      data () {
        return {
          quill: null,
          data: ''
        }
      },
      props: {
        visible: {
          type: Boolean,
          default: true
        },
        content: {
          type: String,
          default: ''
        }
      },
      mounted () {
        this.quill = new Quill(this.$refs.editor, {
          modules: {
            toolbar: {
              container: [[{header: [1, 2, 3, 4, 5, 6, false]}],
                ['bold', 'italic']]
            }
          },
          theme: 'snow'  // or 'bubble'
        })
        this.quill.setContents(toDelta(this.content))
      },
      watch: {
        content: function () {
          this.quill.setContents(toDelta(this.content))
        }
      },
      methods: {
        submit () {
          this.$emit('submit', fromDelta(this.quill.getContents()))
        }
      }
    }
</script>