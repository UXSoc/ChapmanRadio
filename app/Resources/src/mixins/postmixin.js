import Post from './../entity/post'

export default {
  props: {
    post: {
      type: Post,
      default: new Post({})
    }
  },
  methods: {
    getCategories: function () {
      return this.post.categories
    },
    getTags: function () {
      return this.post.tags
    },
    getToken: function () {
      return this.post.token
    },
    getName: function () {
      return this.post.name
    },
    getSlug: function () {
      return this.post.slug
    },
    getCreatedAt: function () {
      return this.post.created_at
    },
    getUpdatedAt: function () {
      return this.post.updated_at
    },
    getContent: function () {
      return this.post.content
    }
  }
}
