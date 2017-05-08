import './vendor'

import Markdown from './dashboard/Markdown.vue'

$(document).ready(function () {
  $(document).foundation()
  new Vue({
    el : '#dashboard',
    components: {
      Markdown
    }
  })
})

