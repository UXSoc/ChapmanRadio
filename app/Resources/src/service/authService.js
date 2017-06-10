/* global Routing */
import axios from 'axios'
import Pagination from './../entity/pagination'
import Post from './../entity/post'
import Envelope from './../entity/envelope'
import Util from './util'
import User from './../entity/user'

export default {
  getStatus: function (responseCallback : (result: Envelope<Pagination<Post>>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_user_status')).then((response) => {
      responseCallback(new Envelope((userData) => new User(userData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  }
}
