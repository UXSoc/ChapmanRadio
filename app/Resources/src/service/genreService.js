/* @flow */
/* global Routing */
import axios from 'axios'
import qs from 'qs'

export default {
  getGenres: function (callback: (result: [string]) => void, search: string = '') {
    return axios.get(Routing.generate('get_genres') + '?' + qs.stringify({ search: search })).then((response) => {
      callback(response.data.categories)
    })
  }
}
