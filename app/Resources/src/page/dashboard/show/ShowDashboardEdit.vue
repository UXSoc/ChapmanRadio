<template>
    <div>Hello this is some text</div>
</template>

<script>
    /* @flow */

    import TagCollection from '../../../components/TagCollection.vue'
    import PermaLink from '../../../components/PermaLink.vue'
    import CheckedCollection from '../../../components/CheckedCollection.vue'
    import QuillEditor from '../../../components/quillEditor.vue'

    export default{
      data () {
        return {
          token: '',
          slug: '',
          quill: {
            toolbar: {
              container: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],

                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'script': 'sub' }, { 'script': 'super' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }]
              ]
            }
          }
        }
      },
      created () {
        if (this.$route.params.token) {
          if (this.$route.params.token) {
            this.token = this.$route.params.token
            this.slug = this.$route.params.slug
            this.edit = true
          }
          this.query()
        }
      },
      methods: {
        update () {
        },
        query () {
          if (this.edit === true) {
            const _this = this
//            PostService.getPost(this.token, this.slug, (post) => {
//              _this.$set(_this, 'post', post)
//            }, 'delta')
//
//            CategoryService.getCategories((categories) => {
//              _this.$set(_this, 'categories', categories)
//            })
          }
        },
        submit () {
        }
      },
      components: {
        TagCollection,
        CheckedCollection,
        PermaLink,
        QuillEditor
      }
    }
</script>