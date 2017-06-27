/* @flow */
import Envelope from '../entity/envelope'

export default {
  handleErrorResponse: function (error: any, errorCallback : (result: Envelope) => void) {
    if (error.response) {
      errorCallback(new Envelope(() => null, error.response.data))
    } else if (error.request) {
      console.log(error.request)
    } else {
      console.log('Error', error.message)
    }
    console.log(error.config)
  }
}
