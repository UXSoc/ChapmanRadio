<template>
    <div>
        <div ref="editor"></div>
        <tag-collection tags="tags"></tag-collection>
        <button>Submit</button>
    </div>
</template>

<script>
  import Quill from '../../../quill/quill'
  import TagCollection from '../../../components/TagCollection.vue'
  export default{
    data () {
      return {
        edit: false,
        quill: null,
        tags: []
      }
    },
    created () {
      if (this.$route.params.token) {
        this.edit = true
      }
    },
    mounted () {
      this.quill = new Quill(this.$refs.editor, {
        modules: {
          toolbar: {
            container: [
              ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
              ['blockquote', 'code-block'],

              [{ 'header': 1 }, { 'header': 2 }],               // custom button values
              [{ 'list': 'ordered'}, { 'list': 'bullet' }],
              [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
              [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
              [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

              [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
              [{ 'font': [] }],
              [{ 'align': [] }]
            ]
          }
        },
        theme: 'snow'  // or 'bubble'
      })
    },
    methods: {
      query () {
      }
    },
    components: {
      TagCollection
    }
  }
</script>