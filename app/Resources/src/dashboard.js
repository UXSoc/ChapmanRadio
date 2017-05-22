
import './vendor'
import UserTable from './dashboard/UserTable.vue'
import ShowTable from './dashboard/ShowTable.vue'

$(document).ready(function () {
  new Vue({
    el: '#page-wrapper',
    components: {
      UserTable,
      ShowTable
    }
  })


  tinymce.init({
    selector: '.markdown-editor',  // change this value according to your HTML
    plugin: 'textpattern',
    textpattern_patterns: [
      {start: '*', end: '*', format: 'italic'},
      {start: '**', end: '**', format: 'bold'},
      {start: '#', format: 'h1'},
      {start: '##', format: 'h2'},
      {start: '###', format: 'h3'},
      {start: '####', format: 'h4'},
      {start: '#####', format: 'h5'},
      {start: '######', format: 'h6'},
      {start: '1. ', cmd: 'InsertOrderedList'},
      {start: '* ', cmd: 'InsertUnorderedList'},
      {start: '- ', cmd: 'InsertUnorderedList'}
    ]
  })

  $('#side-menu').metisMenu({toggle: false})

  $(window).bind('load resize', function () {
    var topOffset = 50
    var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width
    if (width < 768) {
      $('div.navbar-collapse').addClass('collapse')
      topOffset = 100 // 2-row-menu
    } else {
      $('div.navbar-collapse').removeClass('collapse')
    }

    var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1
    height = height - topOffset
    if (height < 1) height = 1
    if (height > topOffset) {
      $('#page-wrapper').css('min-height', (height) + 'px')
    }
  })
})
