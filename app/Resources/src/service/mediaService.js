/* @flow */
/* global Routing */
/* global FormData */

import axios from 'axios'
import Media from './../entity/media'
import Form from './../entity/form'

export default {
  postMedia: function (media: Media, callback: (result: Media | Form) => void) {
    const formData = new FormData()
    formData.append('media[file]', media.file)
    formData.append('media[title]', media.title)
    formData.append('media[caption]', media.caption)
    formData.append('media[altText]', media.altText)
    formData.append('media[description]', media.description)
    return axios.post(Routing.generate('post_media'), formData).then((response) => {
      callback(new Media(response.data.media))
    }).catch((error) => {
      if (error.response) {
        if (error.response.status === 500) {
          callback(new Form(error.response.data))
        }
      }
    })
  }
}
