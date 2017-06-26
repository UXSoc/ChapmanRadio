/* @flow */
/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Pagination from '../entity/pagination'
import Show from './../entity/show'
import Envelope from './../entity/envelope'
import Datatable from './../entity/dataTable'
import Comment from './../entity/comment'

export default {
  getShowsDatatable: function (page: number, sort: [], responseCallback: (result: Envelope<Datatable<Pagination<Show>>>) => void, errorResponseCallback: (result: Envelope) => void, filter: any = {}) {
    const result = Object.assign({ page: page, sort: sort }, filter)
    return axios.get(Routing.generate('get_show_dataTable') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Datatable((paginationData) => new Pagination((postData) => new Show(postData), paginationData), response.data.payload))
    })
  },
  getShows: function (page: number, responseCallback: (result: Pagination<Show>) => void, filter: any = {}) {
    const result = Object.assign({ page: page }, filter)
    return axios.get(Routing.generate('get_shows') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Pagination((postData) => new Show(postData), response.data.payload))
    })
  },
  getShow: function (token: string, slug: string, responseCallback: (result: Envelope<Show>) => void) {
    return axios.get(Routing.generate('get_show', { token: token, slug: slug })).then((response) => {
      responseCallback((postData) => new Show(response.data.show))
    })
  },
  getShowComments: function (token: string, slug: string, root: string, responseCallback: (result: Envelope<Comment>) => void) {
    return axios.get(Routing.generate('get_show_comment', { token: token, slug: slug, comment_token: root })).then((response) => {
      responseCallback(response.data.comments.map((r) => new Comment(r)))
    })
  },
  postPostComment: function (show: Show, comment: string, parentComment: (Comment | null), responseCallback: (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    const params: any = { token: show.getToken(), slug: show.getSlug() }
    if (parentComment) { params['comment_token'] = parentComment.getToken() }
    return axios.post(Routing.generate('post_show_comment', params), qs.stringify({ 'content': comment })).then((response) => {
      responseCallback(new Comment(response.data.comment))
    })
  }
}
