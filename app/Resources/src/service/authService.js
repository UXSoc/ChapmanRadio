/* @flow */
/* global Routing */
import axios from 'axios'
import User from './../entity/user'
import Form from './../entity/form'
import qs from 'qs'

export default {
  getStatus: function (callback: (callback: User) => void) {
    return axios.get(Routing.generate('get_user_status')).then((response) => {
      callback(new User(response.data.user))
    })
  },
  profile: function (payload: any) {
  },
  login: function (username: string, password: string, rememberMe: boolean, callback: (result) => void) {
    return axios.post('/login', qs.stringify({
      '_username': username,
      '_password': password,
      '_remember_me': rememberMe
    })).then(function (response) {
      callback(new Form(response.data))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 400) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  register: function (payload, callback: (result: Form) => void) {
    return axios.post(Routing.generate('post_register'), { user: payload }).then(function (response) {
      callback(new Form(response.data))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 400) {
          callback(new Form(error.response.data))
        }
      }
    })
  }
}
