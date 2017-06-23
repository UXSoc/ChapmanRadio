/* global Routing */
import axios from 'axios'
import Envelope from './../entity/envelope'
import Util from './util'
import qs from 'qs'

export default {
  getCategories: function (responseCallback: (result: Envelope) => void, errorResponseCallback: (result: Envelope) => void, search: string = '') {
    axios.get(Routing.generate('get_categories') + '?' + qs.stringify({search: search})).then((response) => {
      responseCallback(new Envelope((categories) => categories, response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  }
}
