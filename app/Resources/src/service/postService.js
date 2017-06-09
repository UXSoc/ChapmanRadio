/* global Routing */
import axios from 'axios'
import qs from 'qs'
import Pagination from './../entity/pagination'
import Post from './../entity/post'

export default {
  /* @Flow */
  getPosts: function (page, callback, erroCallback, filter = {}) {
    let result = Object.assign({page: page}, filter)
    axios.get(Routing.generate('get_posts') + '?' + qs.stringify(result)).then((response) => {
      callback(new Pagination((out) => new Post(out), response.data.data))
    }).catch((error) => {
      erroCallback(error)
    })
  }
}
