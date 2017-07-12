// @flow
import Vue from 'vue'
import Config from './config'
import VueRouter from 'vue-router'
import VeeValidate from 'vee-validate'
import 'bootstrap-sass'
import 'toastr/toastr'

window.Vue = Vue
window.FConfig = Config
window.VueRouter = VueRouter

Vue.use(VueRouter)
Vue.use(VeeValidate)
