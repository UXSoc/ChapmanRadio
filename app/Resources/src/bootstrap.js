
import Vue from 'vue'
import $ from 'jquery'
import VueRouter from 'vue-router'
import Config from './config'

window.Vue = Vue
window.jQuery = window.$ = $
window.VueRouter = VueRouter
window.FConfig = Config

Vue.use(VueRouter)
