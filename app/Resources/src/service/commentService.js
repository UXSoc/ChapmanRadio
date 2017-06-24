/* @flow */
/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Envelope from './../entity/envelope'
import Comment from './../entity/comment'
import Util from './util'

export default {
  patchComment: function (comment: Comment, response: string, responseCallback : (result: Envelope<Comment>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.patch(Routing.generate('patch_comment', { token: comment.getToken() }), qs.stringify({ content: response })).then((response) => {
      responseCallback(new Envelope((commentData) => new Comment(commentData), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  }
}
