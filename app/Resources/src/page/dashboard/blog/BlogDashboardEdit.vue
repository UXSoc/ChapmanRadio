<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <input type="text" v-model="post.name"/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <perma-link :to="post.getRoute()" ></perma-link>
                <tag-collection  :tags="post.tags"></tag-collection>
                <quill-editor :module="quill" v-model="post.content"></quill-editor>
                <textarea v-model="post.excerpt"></textarea>
            </div>
            <div class="col-lg-4">

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div>Updated At: {{post.createdAt}}</div>
                        <div>Created At: {{post.updatedAt}}</div>
                        <button v-on:click.prevent="submit">Submit</button>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Categories
                    </div>
                    <div class="panel-body">
                        <checked-collection :items="categories" :selected="post.categories"></checked-collection>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  /* @flow */
  import TagCollection from '../../../components/TagCollection.vue'
  import PermaLink from '../../../components/PermaLink.vue'
  import CheckedCollection from '../../../components/CheckedCollection.vue'
  import PostService from '../../../service/postService'
  import CategoryService from '../../../service/categoryService'
  import QuillEditor from '../../../components/quillEditor.vue'

  export default{
    data () {
      return {
        edit: false,
        token: '',
        slug: '',
        post: {},
        categories: [],
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
    mounted () {
    },
    methods: {
      update () {
      },
      query () {
        if (this.edit === true) {
          const _this = this
          PostService.getPost(this.token, this.slug, (post) => {
            _this.$set(_this, 'post', post)
          }, 'delta')

          CategoryService.getCategories((categories) => {
            _this.$set(_this, 'categories', categories)
          })
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