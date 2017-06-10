
import Vue from 'vue'
import Config from './config'
import VueRouter from 'vue-router'
import VeeValidate from 'vee-validate'

window.Vue = Vue
window.FConfig = Config
window.VueRouter = VueRouter

// import 'bootstrap-sass/assets/javascripts/bootstrap'

Vue.use(VueRouter)
Vue.use(VeeValidate)
