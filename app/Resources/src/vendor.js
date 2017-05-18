
import Vue from 'vue'
import 'script-loader!jquery'
import VueRouter from 'vue-router'
import Config from './config'

import 'tinymce/tinymce'
import 'tinymce/themes/modern/theme'
import 'tinymce/plugins/spellchecker/index'
import 'tinymce/plugins/textpattern/index'

window.Vue = Vue
window.jQuery = window.$ = $
window.VueRouter = VueRouter
window.FConfig = Config
window.tinymce = tinymce

import debounce from 'throttle-debounce/debounce'

window.debounce = debounce

import 'bootstrap-sass/assets/javascripts/bootstrap'
import 'metisMenu/dist/metisMenu'

Vue.use(VueRouter)



