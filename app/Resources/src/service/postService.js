/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Pagination from './../entity/pagination'
import Post from './../entity/post'
import Envelope from './../entity/envelope'
import Comment from './../entity/comment'
import Util from './util'

export default {
  getPosts: function (page : number, responseCallback : (result: Envelope<Pagination<Post>>) => void, errorResponseCallback: (result: Envelope) => void, filter = {}) {
    let result = Object.assign({page: page}, filter)
    axios.get(Routing.generate('get_posts') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Envelope((paginationData) => new Pagination((postData) => new Post(postData), paginationData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getPost: function (token: string, slug:string, responseCallback : (result: Envelope<Post>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_post', {token: token, slug: slug})).then((response) => {
      responseCallback(new Envelope((postData) => new Post(postData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getPostComments: function (token: string, slug:string, root: string, responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_blog_comment', {token: token, slug: slug, comment_token: root})).then((response) => {
      responseCallback(new Envelope((commentData) => new Comment(commentData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  postPostComment: function () {
  },
  patchPost: function () {
  }
}
