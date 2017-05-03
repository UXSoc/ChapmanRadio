import './bootstrap'
import App from './App.vue'

import SummaryPage from './pages/SummaryPage.vue'
const router = new VueRouter({
  routes: [
     { name: 'summary', path: '/', component: SummaryPage }
  ]
})

window.onload = function () {
  // router.push({name : "summary"})
  new Vue({
    router,
    render: h => h(App)
  }).$mount('#app')
}
