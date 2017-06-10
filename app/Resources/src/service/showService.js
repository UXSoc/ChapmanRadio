/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Pagination from './../entity/pagination'
import Show from './../entity/show'
import Envelope from './../entity/envelope'
import Util from './util'

export default {
  getShows: function (page : number, responseCallback : (result: Envelope<Pagination<Show>>) => void, errorResponseCallback: (result: Envelope) => void, filter = {}) {
    let result = Object.assign({page: page}, filter)
    axios.get(Routing.generate('get_shows') + '?' + qs.stringify(result)).then((response) => {
      responseCallback(new Envelope((paginationData) => new Pagination((postData) => new Show(postData), paginationData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getShow: function (token: string, slug:string, responseCallback : (result: Envelope<Show>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_show', {token: token, slug: slug})).then((response) => {
      responseCallback(new Envelope((postData) => new Show(postData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  }
}
