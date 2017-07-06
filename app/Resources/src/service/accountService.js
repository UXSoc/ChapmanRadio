// @flow
/* global Routing */
/* global FormData */
import axios from 'axios'
import qs from 'qs'
import Form from './../entity/form'
import FormView from './../entity/formView'

export default {
  postChangePassword: function ( oldPassword: string, newPassword: string, callback: (result: Form) => void) {
    return axios.post(Routing.generate('post_account_password'), qs.stringify({
      oldPassword: oldPassword,
      newPassword: newPassword
    })).then((response) => {
      callback(response.data)
    })
  },
  postImage: function (image: File, x: number, y: number, width: number, height: number, callback: (result:string) => void) {
    const formData = new FormData()
    formData.append('profile_image[image]', image)
    formData.append('profile_image[x]', x)
    formData.append('profile_image[y]', y)
    formData.append('profile_image[width]', width)
    formData.append('profile_image[height]', height)
    return axios.post(Routing.generate('post_account_profile_image'), formData, {
      headers: { 'content-type': 'multipart/form-data' }
    }).then((response) => {
      callback(response.data.path)
    })
  }
}
