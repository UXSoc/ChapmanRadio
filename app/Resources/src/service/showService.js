/* @flow */
/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Pagination from '../entity/pagination'
import Show from './../entity/show'
import Datatable from './../entity/dataTable'
import Comment from './../entity/comment'
import Form from './../entity/form'

export default {
  getShowsDatatable: function (page: number, sort: [], callback: (result: Datatable<Pagination<Show>>) => void, filter: any = {}) {
    const result = Object.assign({ page: page, sort: sort }, filter)
    return axios.get(Routing.generate('get_show_dataTable') + '?' + qs.stringify(result)).then((response) => {
      callback(new Datatable((paginationData) => new Pagination((postData) => new Show(postData), paginationData), response.data.datatable))
    })
  },
  getShows: function (page: number, responseCallback: (result: Pagination<Show>) => void, filter: any = {}) {
    const result = Object.assign({ page: page }, filter)
    return axios.get(Routing.generate('get_shows') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Pagination((postData) => new Show(postData), response.data.payload))
    })
  },
  getShow: function (token: string, slug: string, callback: (result: Show) => void, parse: string = 'HTML') {
    return axios.get(Routing.generate('get_show', { token: token, slug: slug }) + '?' + qs.stringify({ delta: parse })).then((response) => {
      callback(new Show(response.data.show))
    })
  },
  getShowComments: function (show:Show, root: (Comment | null), callback : (result: [Comment]) => void) {
    let commentToken = null
    if (root !== null) {
      commentToken = root.token
    }
    return axios.get(Routing.generate('get_show_comment', { token: show.token, slug: show.slug, comment_token: commentToken })).then((response) => {
      callback(response.data.comments.map((r) => new Comment(r)))
    })
  },
  postShowComment: function (show: Show, comment: string, parentComment: (Comment | null), callback: (result: (Comment | Form)) => void) {
    const payload: {
      parentComment: ?string,
      content: string
    } = { content: comment }
    if (root !== null) {
      payload.parentComment = root.token
    }
    return axios.post(Routing.generate('post_show_comment', { token: show.token, slug: show.slug }), qs.stringify({ 'comment': payload })).then((response) => {
      callback(new Comment(response.data.comment))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 400) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  postShow: function (post: Show, callback : (result: Form | Show) => void) {
    return axios.post(Routing.generate('post_show'), qs.stringify(post.payload)).then((response) => {
      callback(new Show(response.data.show))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 400) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  patchShow: function (token: string, slug:string, post: Form | Show, callback : (result: Form) => void) {
    return axios.patch(Routing.generate('patch_show', { token: token, slug: slug }), qs.stringify(post.payload)).then((response) => {
      callback(new Show(response.data.show))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 400) {
          callback(new Form(error.response.data))
        }
      }
    })
  }
}
