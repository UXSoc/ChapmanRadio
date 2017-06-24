/* @flow */
/* global Routing */
import axios from 'axios'
import Post from './../entity/post'
import Envelope from './../entity/envelope'
import Util from './util'
import User from './../entity/user'
import qs from 'qs'
import Pagination from './../entity/pagination'

export default {
  getStatus: function (responseCallback: (result: Envelope<Pagination<Post>>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_user_status')).then((response) => {
      responseCallback(new Envelope((userData) => new User(userData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  login: function (responseCallback: (result: Envelope) => void, errorResponseCallback: (result: Envelope) => void, username: string, password: string, rememberMe: boolean) {
    axios.post('/login', qs.stringify({
      '_username': username,
      '_password': password,
      '_remember_me': rememberMe
    })).then(function (response) {
      responseCallback(new Envelope(() => null, response.data))
    }).catch(function (error) {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  }
}
