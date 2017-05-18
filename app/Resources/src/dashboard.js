
import './vendor'
import {prepareSidebar} from './dashboard/dashboard-sidebar'
import {prepareEditor} from './dashboard/editor'

$(document).ready(function () {
  new Vue({
    el: '#page-wrapper',
    components: {
    }
  })
  prepareEditor()
  prepareSidebar()
})
