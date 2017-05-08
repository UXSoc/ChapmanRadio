
import Vue from 'vue'
import 'script-loader!jquery'
import VueRouter from 'vue-router'
import Config from './config'

window.Vue = Vue
window.jQuery = window.$ = $
window.VueRouter = VueRouter
window.FConfig = Config

import debounce from 'throttle-debounce/debounce'

window.debounce = debounce
import 'tinymce/tinymce'
import 'tinymce/themes/modern/theme'
import 'tinymce/plugins/spellchecker/index'
import 'tinymce/plugins/textpattern/index'

import 'foundation-sites/dist/js/foundation'
import 'foundation-sites/js/foundation.core'
import 'foundation-sites/js/foundation.util.mediaQuery'
import 'foundation-sites/js/foundation.util.motion'
import 'foundation-sites/js/foundation.util.keyboard'
import 'foundation-sites/js/foundation.util.triggers'
import 'foundation-sites/js/foundation.offcanvas'

Vue.use(VueRouter)

