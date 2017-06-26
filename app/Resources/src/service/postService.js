/* @flow */
/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Pagination from '../entity/pagination'
import Post from './../entity/post'
import Envelope from './../entity/envelope'
import Comment from './../entity/comment'
import Datatable from './../entity/dataTable'
import Form from './../entity/form'

export default {
  getPostsDatatable: function (page : number, sort : [], responseCallback : (result: Envelope<Datatable<Pagination<Post>>>) => void, filter: any = {}) {
    const result = Object.assign({ page: page, sort: sort }, filter)
    return axios.get(Routing.generate('get_post_dataTable') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Envelope((dataTableData) => new Datatable((paginationData) => new Pagination((postData) => new Post(postData), paginationData), dataTableData), response.data.payload))
    })
  },
  getPosts: function (page : number, responseCallback : (result: Pagination<Post>) => void, filter: any = {}) {
    const result = Object.assign({ page: page }, filter)
    return axios.get(Routing.generate('get_posts') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Pagination((postData) => new Post(postData), response.data.payload))
    })
  },
  getPost: function (token: string, slug:string, callback : (result: Post) => void, parse: string = 'HTML') {
    return axios.get(Routing.generate('get_post', { token: token, slug: slug }) + '?' + qs.stringify({ delta: parse })).then((response) => {
      callback(new Post(response.data.post))
    })
  },
  patchPost: function (post: Post, responseCallback : (result: Envelope<Post>) => void, delta: boolean = false) {
    const payload = {
      name: post.name,
      content: post.content,
      excerpt: post.excerpt,
      slug: post.slug,
      isPinned: post.isPinned
    }
    return axios.patch(Routing.generate('patch_post', { token: post.token, slug: post.slug }) + '?' + qs.stringify(payload)).then((response) => {
      responseCallback(new Envelope((postData) => new Post(postData), response.data))
    })
  },
  getPostComments: function (post:Post, root: (Comment | null), callback : (result: Envelope<Comment>) => void) {
    let commentToken = null
    if (root !== null) {
      commentToken = root.token
    }
    return axios.get(Routing.generate('get_blog_comment', { token: post.token, slug: post.slug, comment_token: commentToken })).then((response) => {
      callback(response.data.comments.map((r) => new Comment(r)))
    })
  },
  postPostComment: function (post: Post, comment: string, root: (Comment | null), callback : (result: Envelope<Comment>) => void) {
    const payload: {
      parentComment: ?string,
      content: string
    } = {}
    if (root !== null) {
      payload.parentComment = root.token
    }
    payload.content = comment
    return axios.post(Routing.generate('post_post_comment', { token: post.token, slug: post.slug }), qs.stringify({ 'comment': payload })).then((response) => {
      callback(new Comment(response.data.comment))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 400) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  getPostTags: function (token: string, slug: string, responseCallback : (result: Envelope<Comment>) => void) {
    return axios.get(Routing.generate('get_post_tags', { token: token, slug: slug })).then((response) => {
      responseCallback(new Envelope((tags) => tags, response.data))
    })
  },
  getPostCategories: function (token: string, slug: string, responseCallback : (result: Envelope<Comment>) => void) {
    return axios.get(Routing.generate('get_post_categories', { token: token, slug: slug })).then((response) => {
      responseCallback(new Envelope((categories) => categories, response.data))
    })
  },
  deletePostTag: function (post: Post, tag: string, responseCallback : (result: Envelope<Comment>) => void) {
    return axios.delete(Routing.generate('delete_tag_post', { token: post.getToken(), slug: post.getSlug(), tag: tag })).then((response) => {
      responseCallback(new Envelope((tag) => tag, response.data))
    })
  },
  putPostTag: function (post: Post, tag: string, responseCallback : (result: Envelope<Comment>) => void) {
    return axios.put(Routing.generate('put_tag_post', { token: post.getToken(), slug: post.getSlug(), tag: tag })).then((response) => {
      responseCallback(new Envelope((tagData) => tagData, response.data))
    })
  },
  deletePostCategory: function (post: Post, category: string, responseCallback : (result: Envelope<Comment>) => void) {
    return axios.delete(Routing.generate('delete_category_post', { token: post.getToken(), slug: post.getSlug(), category: category })).then((response) => {
      responseCallback(new Envelope((category) => category, response.data))
    })
  },
  putPostCategory: function (post: Post, category: string, responseCallback : (result: Envelope<Comment>) => void) {
    return axios.put(Routing.generate('put_category_post', { token: post.getToken(), slug: post.getSlug(), category: category })).then((response) => {
      responseCallback(new Envelope((category) => category, response.data))
    })
  }
}
