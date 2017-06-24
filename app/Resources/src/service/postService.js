/* @flow */
/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Pagination from '../entity/pagination'
import Post from './../entity/post'
import Envelope from './../entity/envelope'
import Comment from './../entity/comment'
import Datatable from './../entity/dataTable'
import Util from './util'

export default {
  getPostsDatatable: function (page : number, sort : [], responseCallback : (result: Envelope<Datatable<Pagination<Post>>>) => void, errorResponseCallback: (result: Envelope) => void, filter: any = {}) {
    const result = Object.assign({ page: page, sort: sort }, filter)
    axios.get(Routing.generate('get_post_dataTable') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Envelope((dataTableData) => new Datatable((paginationData) => new Pagination((postData) => new Post(postData), paginationData), dataTableData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getPosts: function (page : number, responseCallback : (result: Envelope<Pagination<Post>>) => void, errorResponseCallback: (result: Envelope) => void, filter: any = {}) {
    const result = Object.assign({ page: page }, filter)
    axios.get(Routing.generate('get_posts') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Envelope((paginationData) => new Pagination((postData) => new Post(postData), paginationData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getPost: function (token: string, slug:string, responseCallback : (result: Envelope<Post>) => void, errorResponseCallback: (result: Envelope) => void, delta: boolean = false) {
    axios.get(Routing.generate('get_post', { token: token, slug: slug }) + '?' + qs.stringify({ delta: delta })).then((response) => {
      responseCallback(new Envelope((postData) => new Post(postData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  patchPost: function (post: Post, responseCallback : (result: Envelope<Post>) => void, errorResponseCallback: (result: Envelope) => void, delta: boolean = false) {
    const payload = {
      name: post.name,
      content: post.content,
      excerpt: post.excerpt,
      slug: post.slug,
      isPinned: post.isPinned
    }
    axios.patch(Routing.generate('patch_post', { token: post.token, slug: post.slug }) + '?' + qs.stringify(payload)).then((response) => {
      responseCallback(new Envelope((postData) => new Post(postData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getPostComments: function (token: string, slug:string, root: string, responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_blog_comment', { token: token, slug: slug, comment_token: root })).then((response) => {
      responseCallback(new Envelope((commentData) => commentData.map((r) => new Comment(r)), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  postPostComment: function (post: Post, comment: string, parentComment: (Comment | null), responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    const params: any = { token: post.getToken(), slug: post.getSlug() }
    if (parentComment) { params['comment_token'] = parentComment.getToken() }
    axios.post(Routing.generate('post_post_comment', params), qs.stringify({ 'content': comment })).then((response) => {
      responseCallback(new Envelope((commentData) => new Comment(commentData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getPostTags: function (token: string, slug: string, responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_post_tags', { token: token, slug: slug })).then((response) => {
      responseCallback(new Envelope((tags) => tags, response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getPostCategories: function (token: string, slug: string, responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_post_categories', { token: token, slug: slug })).then((response) => {
      responseCallback(new Envelope((categories) => categories, response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  deletePostTag: function (post: Post, tag: string, responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.delete(Routing.generate('delete_tag_post', { token: post.getToken(), slug: post.getSlug(), tag: tag })).then((response) => {
      responseCallback(new Envelope((tag) => tag, response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  putPostTag: function (post: Post, tag: string, responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.put(Routing.generate('put_tag_post', { token: post.getToken(), slug: post.getSlug(), tag: tag })).then((response) => {
      responseCallback(new Envelope((tagData) => tagData, response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  deletePostCategory: function (post: Post, category: string, responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.delete(Routing.generate('delete_category_post', { token: post.getToken(), slug: post.getSlug(), category: category })).then((response) => {
      responseCallback(new Envelope((category) => category, response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  putPostCategory: function (post: Post, category: string, responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.put(Routing.generate('put_category_post', { token: post.getToken(), slug: post.getSlug(), category: category })).then((response) => {
      responseCallback(new Envelope((category) => category, response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  }
}
