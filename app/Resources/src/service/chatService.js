// @flow
/* global Routing */
import axios from 'axios'
import Form from './../entity/form'
import Token from './../entity/token'

export default {
  getChatToken: function (callback: (result: Form) => void) {
    return axios.post(Routing.generate('get_chat_token')).then((response) => {
      callback(new Token(response.data))
    })
  }
}
