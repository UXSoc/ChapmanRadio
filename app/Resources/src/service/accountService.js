// @flow
/* global Routing */
/* global FormData */
import axios from 'axios'
import qs from 'qs'
import Form from './../entity/form'
import Profile from './../entity/profile'

export default {
  postChangePassword: function (oldPassword: string, newPassword: string, callback: (result: Form) => void) {
    return axios.post(Routing.generate('post_account_password'), qs.stringify({
      reset_password: {
        oldPassword: oldPassword,
        newPassword: newPassword
      }})).then((response) => {
      callback(new Form(response.data))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 400) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  patchProfile: function (profile: Profile, callback: (result : Profile|Form) => void) {
    return axios.patch(Routing.generate('patch_profile'), qs.stringify({
      profile: {
        'firstName': profile.firstName,
        'lastName': profile.lastName
      }
    })).then(function (response) {
      callback(new Profile(response.data.profile))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 400) {
          callback(new Form(error.response.data))
        }
      }
    })
  },
  getProfile: function (callback: (result : Profile) => void) {
    return axios.get(Routing.generate('get_profile')).then(function (response) {
      callback(new Profile(response.data.profile))
    })
  },
  postImage: function (image: File, x: number, y: number, width: number, height: number, callback: (result:string) => void) {
    const formData = new FormData()
    formData.append('profile_image[image]', image)
    formData.append('profile_image[x]', x)
    formData.append('profile_image[y]', y)
    formData.append('profile_image[width]', width)
    formData.append('profile_image[height]', height)
    return axios.post(Routing.generate('post_profile_image'), formData, {
      headers: { 'content-type': 'multipart/form-data' }
    }).then((response) => {
      callback(response.data.path)
    })
  }
}
