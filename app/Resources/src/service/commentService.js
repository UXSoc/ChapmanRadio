/* @flow */
/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Comment from './../entity/comment'

export default {
  patchComment: function (comment: Comment, response: string, callback : (result: Comment) => void) {
    return axios.patch(Routing.generate('patch_comment', { token: comment.token }), qs.stringify({ comment: { content: response }})).then((response) => {
      callback(new Comment(response.data.comment))
    })
  }
}
