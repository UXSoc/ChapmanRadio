<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <input type="text" v-model="post.name"/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <perma-link :to="{name: 'post_single', params: {token: post.token, slug: post.slug}}" ></perma-link>
                <tag-collection  :tags="post.tags"></tag-collection>
                <div ref="editor" class="quill-dashboard-editor"></div>
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
  /* global Routing */
  import Quill from '../../../quill/quill'
  import TagCollection from '../../../components/TagCollection.vue'
  import PermaLink from '../../../components/PermaLink.vue'
  import CheckedCollection from '../../../components/CheckedCollection.vue'
  import Post from '../../../entity/post'
  import axios from 'axios'
  import qs from 'qs'

  export default{
    data () {
      return {
        edit: false,
        quill: null,
        token: '',
        slug: '',
        post: {},
        categories: []
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
      this.quill = new Quill(this.$refs.editor, {
        modules: {
          toolbar: {
            container: [
              ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
              ['blockquote', 'code-block'],

              [{'header': 1}, {'header': 2}],               // custom button values
              [{'list': 'ordered'}, {'list': 'bullet'}],
              [{'script': 'sub'}, {'script': 'super'}],      // superscript/subscript
              [{'indent': '-1'}, {'indent': '+1'}],          // outdent/indent
              [{'header': [1, 2, 3, 4, 5, 6, false]}],

              [{'color': []}, {'background': []}],          // dropdown with defaults from theme
              [{'font': []}],
              [{'align': []}]
            ]
          }
        },
        theme: 'snow'  // or 'bubble'
      })
      let _this = this
      this.quill.on('text-change', function (delta, oldDelta, source) {
        _this.post.content = delta
      })
    },
    methods: {
      update () {
      },
      query () {
        if (this.edit === true) {
          let _this = this
          axios.get(Routing.generate('get_post', {token: this.token, slug: this.slug}) + '?' + qs.stringify({delta: true})).then((response) => {
            _this.$set(_this, 'post', response.data)
          }).catch((error) => {
          })

//          CategoryService.getCategories(function (e) {
//            _this.$set(_this, 'categories', e.getResult())
//          }, function (e) {
//          })
        }
      },
      submit () {
      }
    },
    components: {
      TagCollection,
      CheckedCollection,
      PermaLink
    }
  }
</script>