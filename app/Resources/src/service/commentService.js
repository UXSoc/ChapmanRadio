/* @flow */
/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Envelope from './../entity/envelope'
import Comment from './../entity/comment'

export default {
  patchComment: function (comment: Comment, response: string, callback : (result: Envelope<Comment>) => void) {
    return axios.patch(Routing.generate('patch_comment', { token: comment.getToken() }), qs.stringify({ content: response })).then((response) => {
      callback(new Comment(response.data.comment))
    })
  }
}
