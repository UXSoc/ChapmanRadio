<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <input type="text"  v-on:blur="unBlurName()" v-model="post.name"/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <perma-link v-if="post  && post.slug !== ''" v-model="post.slug" :to="post.getRoute()" ></perma-link>
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
                        <div v-for="category in categories">
                            <input type="checkbox" :value="category" v-model="post.categories"/>{{category}}
                        </div>
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
  import PostService from '../../../service/postService'
  import CategoryService from '../../../service/categoryService'
  import QuillEditor from '../../../components/quillEditor.vue'
  import Post from '../../../entity/post'
  import Form from '../../../entity/form'

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
      }
      this.query()
    },
    mounted () {
    },
    methods: {
      update () {
      },
      unBlurName () {
        if (this.post && this.post.name !== '' && this.post.slug === '') {
          this.post.slug = this.post.name
        }
      },
      query () {
        const _this = this
        if (this.edit === true) {
          PostService.getPost(this.token, this.slug, (post) => {
            _this.$set(_this, 'post', post)
          }, 'delta')
        } else {
          _this.$set(_this, 'post', new Post({}))
        }

        CategoryService.getCategories((categories) => {
          _this.$set(_this, 'categories', categories)
        })
      },
      submit () {
        const _this = this
        if (_this.edit === true) {
          PostService.patchPost(_this.token, _this.slug, this.post, (post) => {
            if (post instanceof Post) {
              _this.$set(_this, 'post', post)
            } else if (post instanceof Form) {
            }
          })
        } else {
          PostService.postPost(this.post, (post) => {
            if (post instanceof Post) {
              _this.$set(_this, 'post', post)
              _this.$router.push(post.getRoute())
            } else if (post instanceof Form) {
            }
          })
        }
      }
    },
    components: {
      TagCollection,
      PermaLink,
      QuillEditor
    }
  }
</script>