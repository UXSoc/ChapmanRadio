<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1>Blog</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <perma-link v-if="post" :to="post.getRoute()" ></perma-link>
                <tag-collection @onItemRemoved="removeTag" @onItemAdded="addTag" :collection="tags">
                    <template slot="tag" scope="props">
                        <span class="tag label label-info">{{props.tag.tag}}<a v-on:click.prevent="props.removeTag"><i class="fa fa-times" aria-hidden="true"></i></a></span>
                    </template>
                </tag-collection>
                <div ref="editor" class="quill-dashboard-editor"></div>
            </div>
            <div class="col-lg-4">

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div>Updated At: </div>
                        <div>Created At: </div>
                        <button v-on:click.prevent="submit">Submit</button>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Categories
                    </div>
                    <div class="panel-body">
                        <checked-collection :collection="categories" :selectedItems="selectedCategories" :isSelected="isCategorySelected">
                            <template slot="item" scope="props">
                                {{props.item.category}}
                            </template>
                        </checked-collection>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Quill from '../../../quill/quill'
  import TagCollection from '../../../components/TagCollection.vue'
  import PermaLink from '../../../components/PermaLink.vue'
  import CheckedCollection from '../../../components/CheckedCollection.vue'
  import PostService from '../../../service/postService'
  import CategoryService from '../../../service/categoryService'
  import Tag from '../../../entity/tag'
  export default{
    data () {
      return {
        edit: false,
        quill: null,
        tags: [],
        token: '',
        slug: '',
        post: null,
        categories: [],
        selectedCategories: []
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
        this.queryCategories()
        this.queryTags()
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
    },
    methods: {
      update () {
      },
      query () {
        if (this.edit === true) {
          let _this = this
          PostService.getPost(this.token, this.slug, function (e) {
            _this.$set(_this, 'post', e.getResult())
          }, function (e) {
          }, true)
        }
      },
      queryTags () {
        if (this.edit === true) {
          let _this = this
          PostService.getPostTags(this.token, this.slug, function (e) {
            _this.$set(_this, 'tags', e.getResult())
          }, (e) => {
          })
        }
      },
      queryCategories () {
        let _this = this
        CategoryService.getCategories(function (e) {
          _this.$set(_this, 'categories', e.getResult())
        }, function (e) {
        })

        if (_this.edit === true) {
          PostService.getPostCategories(_this.token, _this.slug, function (e) {
            _this.$set(_this, 'selectedCategories', e.getResult())
          }, (e) => {
          })
        }
      },
      isCategorySelected (items, item) {
        return items.find((n) => n.category === item.category) !== undefined
      },
      addTag (tag: Tag) {
        if (this.tags.find((value) => value.tag === tag.tag) === undefined) {
          this.tags.push(tag)
          this.$set(this, 'tags', this.tags)
        }
        if (this.edit === true) {
          let _this = this
          PostService.putPostTag(this.post, tag, function (e) {
            _this.queryTags()
          }, (e) => {
          })
        }
      },
      removeTag (tag: Tag) {
        for (let i = 0; i < this.tags.length; i++) {
          if (this.tags[i].tag === tag.tag) {
            this.tags.splice(i, 1)
            this.$set(this, 'tags', this.tags)
            break
          }
        }
        if (this.edit === true) {
          let _this = this
          PostService.deletePostTag(this.post, tag, function (e) {
            _this.queryTags()
          }, (e) => {
          })
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