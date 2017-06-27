<template>
    <div>
        <div ref="editor"></div>
    </div>
</template>

<script>
  import Quill from '../quill/quill'
  export default{
    data () {
      return {
        quill: null
      }
    },
    props: {
      value: {
        type: String,
        default: ''
      },
      module: {
        type: Object,
        default: {}
      }
    },
    mounted () {
      const _this = this
      this.quill = new Quill(this.$refs.editor, {
        modules: this.module,
        theme: 'snow'
      })
      this.quill.setContents(JSON.parse(this.value))
      this.quill.on('text-change', function (delta, oldDelta, source) {
        _this.$emit('input', JSON.stringify(_this.quill.getContents()))
      })
    },
    watch: {
      value: function () {
        if (this.value !== JSON.stringify(this.quill.getContents())) {
          this.quill.setContents(JSON.parse(this.value))
        }
      }
    },
    methods: {
    }
  }

</script>