
import Vue from 'vue'
import 'script-loader!jquery'
import Config from './config'

import 'tinymce/tinymce'
import 'tinymce/themes/modern/theme'
import 'tinymce/plugins/spellchecker/index'
import 'tinymce/plugins/textpattern/index'

import axios from 'axios/dist/axios'


window.axios = axios
window.Vue = Vue
window.jQuery = window.$ = $
window.FConfig = Config
window.tinymce = tinymce

import debounce from 'throttle-debounce/debounce'

window.debounce = debounce

import 'bootstrap-sass/assets/javascripts/bootstrap'
import 'metisMenu/dist/metisMenu'







