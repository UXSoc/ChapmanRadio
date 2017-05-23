
import Vue from 'vue'
import 'script-loader!jquery'
import Config from './config'
import VueRouter from 'vue-router'

import 'tinymce/tinymce'
import 'tinymce/themes/modern/theme'
import 'tinymce/plugins/spellchecker/index'
import 'tinymce/plugins/textpattern/index'

import axios from 'axios/dist/axios'

import VeeValidate from 'vee-validate';

window.axios = axios
window.Vue = Vue
window.jQuery = window.$ = $
window.FConfig = Config
window.tinymce = tinymce
window.VueRouter = VueRouter

import debounce from 'throttle-debounce/debounce'

window.debounce = debounce

import 'bootstrap-sass/assets/javascripts/bootstrap'
import 'metisMenu/dist/metisMenu'

Vue.use(VueRouter)
Vue.use(VeeValidate)




