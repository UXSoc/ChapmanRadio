/* @flow */
/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Pagination from '../entity/pagination'
import Show from './../entity/show'
import Envelope from './../entity/envelope'
import Util from './util'
import Datatable from './../entity/dataTable'
import Comment from './../entity/comment'

export default {
  getShowsDatatable: function (page: number, sort: [], responseCallback: (result: Envelope<Datatable<Pagination<Show>>>) => void, errorResponseCallback: (result: Envelope) => void, filter: any = {}) {
    const result = Object.assign({ page: page, sort: sort }, filter)
    axios.get(Routing.generate('get_show_dataTable') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Envelope((dataTableData) => new Datatable((paginationData) => new Pagination((postData) => new Show(postData), paginationData), dataTableData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getShows: function (page: number, responseCallback: (result: Envelope<Pagination<Show>>) => void, errorResponseCallback: (result: Envelope) => void, filter: any = {}) {
    const result = Object.assign({ page: page }, filter)
    axios.get(Routing.generate('get_shows') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Envelope((paginationData) => new Pagination((postData) => new Show(postData), paginationData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getShow: function (token: string, slug: string, responseCallback: (result: Envelope<Show>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_show', { token: token, slug: slug })).then((response) => {
      responseCallback(new Envelope((postData) => new Show(postData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getShowComments: function (token: string, slug: string, root: string, responseCallback: (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_show_comment', { token: token, slug: slug, commentToken: root })).then((response) => {
      responseCallback(new Envelope((commentData) => commentData.map((r) => new Comment(r)), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  postPostComment: function (show: Show, comment: string, parentComment: (Comment | null), responseCallback: (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    const params: any = { token: show.getToken(), slug: show.getSlug() }
    if (parentComment) { params['commentToken'] = parentComment.getToken() }
    axios.post(Routing.generate('post_show_comment', params), qs.stringify({ 'content': comment })).then((response) => {
      responseCallback(new Envelope((commentData) => new Comment(commentData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  }
}
