<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <input type="text" v-on:blur="unBlurName()" v-model="show.name"/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <perma-link v-if="show && show.slug !== ''" v-model="show.slug" :to="show.getRoute()" ></perma-link>
                <tag-collection  :tags="show.tags"></tag-collection>
                <quill-editor :module="quill" v-model="show.description"></quill-editor>
                <textarea v-model="show.excerpt"></textarea>
            </div>
            <div class="col-lg-4">

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div>Updated At: {{show.createdAt}}</div>
                        <div>Created At: {{show.updatedAt}}</div>
                        <button v-on:click.prevent="submit">Submit</button>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Genres
                    </div>
                    <div class="panel-body">
                        <div v-for="category in genres">
                            <input type="checkbox" :value="category" v-model="show.genres"/>{{category}}
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
    import ShowService from '../../../service/showService'
    import QuillEditor from '../../../components/quillEditor.vue'
    import GenreService from '../../../service/genreService'

    export default{
      data () {
        return {
          token: '',
          slug: '',
          genres: [],
          show: {},
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
        unBlurName () {
          if (this.show && this.show.name !== '' && this.show.slug === '') {
            this.show.slug = this.show.name
          }
        },
        query () {
          if (this.edit === true) {
            const _this = this
            ShowService.getShow(this.token, this.slug, (show) => {
              _this.$set(_this, 'show', show)
            }, 'delta')

            GenreService.getGenres((genres) => {
              _this.$set(_this, 'genres', genres)
            })
          }
        },
        submit () {
          const _this = this
          if (_this.edit === true) {
            ShowService.patchShow(_this.token, _this.slug, this.show, (post) => {
              _this.query()
            })
          } else {
            ShowService.postShow(_this.token, _this.slug, this.show, (post) => {
              _this.query()
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