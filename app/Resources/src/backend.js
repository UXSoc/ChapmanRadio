import 'script-loader!jquery'
import 'script-loader!jquery.cookie'


import 'foundation-sites/dist/js/foundation'

import 'foundation-sites/js/foundation.core'
import 'foundation-sites/js/foundation.util.mediaQuery'
import 'foundation-sites/js/foundation.util.motion'
import 'foundation-sites/js/foundation.util.keyboard'
import 'foundation-sites/js/foundation.util.nest'
import 'foundation-sites/js/foundation.accordionMenu'


$( document ).ready(function() {

  $(document).foundation()
  var acc = []

  if (!!$.cookie('token')) {
    acc = $.cookie('token').split(',')
  }

  $('.chapman_radio_main_menu').find("[menu-chapman-key]").each(function () {
    var key =$(this).attr('menu-chapman-key')
    for (var i=0; i<acc.length; i++) {
      console.log(acc[i])
      if (acc[i] === key) {
        $('.chapman_radio_main_menu').foundation('down', $(this),false);
      }
    }
  })

  $('.chapman_radio_main_menu').bind('down.zf.accordionMenu',function (event,sr) {
    acc.push($(sr).attr('menu-chapman-key'))
    $.cookie('token',acc.join())
  });
  $('.chapman_radio_main_menu').bind('up.zf.accordionMenu',function (event,sr) {
    for (var i=acc.length-1; i>=0; i--) {
      if (acc[i] === $(sr).attr('menu-chapman-key')) {
        acc.splice(i, 1)
      }
    }
    $.cookie('token',acc.join())
  });


})
