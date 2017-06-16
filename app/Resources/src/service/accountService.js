/* global Routing */
import axios from 'axios'
import Envelope from './../entity/envelope'
import Util from './util'
import qs from 'qs'

export default {
  postChangePassword: function (oldPassword: string, newPassword: string, responseCallback: (result: Envelope) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.post(Routing.generate('post_account_password'), qs.stringify({
      oldPassword: oldPassword,
      newPassword: newPassword
    })).then((response) => {
      responseCallback(new Envelope((data) => data, response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  }
}
