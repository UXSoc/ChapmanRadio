import './vendor'
import App from './App.vue'

import SummaryPage from './pages/SummaryPage.vue'

import Dashboard from './pages/dashboard/Dashboard.vue'

import AllCommentDashboard from './pages/dashboard/comment/AllCommentDashboard.vue'

import AllPostDashboard from './pages/dashboard/post/AllPostDashboard.vue'
import AddNewPostDashboard from './pages/dashboard/post/AddNewPostDashboard.vue'
import PostCategoryDashboard from './pages/dashboard/post/PostCategoryDashboard.vue'
import PostTagDashboard from './pages/dashboard/post/PostTagDashboard.vue'

import ProfileDashboard from './pages/dashboard/profile/ProfileDashboard.vue'
import ProfileSettingsDashboard from './pages/dashboard/profile/ProfileSettingDashboard.vue'

import SettingDashboard from './pages/dashboard/setting/SettingDashboard.vue'

import AllShowDashboard from './pages/dashboard/show/AllShowDashboard.vue'
import AddNewShowDashboard from './pages/dashboard/show/AddNewShowDashboard.vue'
import ShowCategoryDashboard from './pages/dashboard/show/ShowCategoryDashboard.vue'
import ShowTagDashboard from './pages/dashboard/show/ShowTagDashboard.vue'
import ShowDashboard from './pages/dashboard/show/ShowDashboard.vue'

import AddNewUserDashboard from './pages/dashboard/user/AddNewUserDashboard.vue'
import AllUserDashboard from './pages/dashboard/user/AllUserDashboard.vue'
import UserDashboard from './pages/dashboard/user/UserDashboard.vue'


const router = new VueRouter({
  routes: [
    {name: 'summary', path: '/', component: SummaryPage},
    {
      name: 'dashboard', path: '/dashboard/', component: Dashboard,
      children: [

        // shows
        {name: 'dashboard_all_show', path: 'show', component: AllShowDashboard},
        {name: 'dashboard_add_new_show', path: 'show/add', component: AddNewShowDashboard},
        {name: 'dashboard_category_show', path: 'show/category', component: ShowCategoryDashboard},
        {name: 'dashboard_tag_show', path: 'show/tag', component: ShowTagDashboard},
        {name: 'dashboard_show', path: 'show/:id', component: ShowDashboard},

        // settings
        {name: 'dashboard_setting', path: 'setting', component: SettingDashboard},

        // user
        {name: 'dashboard_add_new_user', path: 'user/new-user', component: AddNewUserDashboard},
        {name: 'dashboard_all_user', path: 'user', component: AllUserDashboard},
        {name: 'dashboard_user', path: 'user/:id', component: UserDashboard},

        // profile
        {name: 'dashboard_profile', path: 'profile', component: ProfileDashboard},
        {name: 'dashboard_profile_setting', path: 'profile/setting', component: ProfileSettingsDashboard},

        // post
        {name: 'dashboard_all_post', path: 'post', component: AllPostDashboard},
        {name: 'dashboard_add_new_post', path: 'post/add', component: AddNewPostDashboard},
        {name: 'dashboard_category_post', path: 'post/category', component: PostCategoryDashboard},
        {name: 'dashboard_tag_post', path: 'post/tag', component: PostTagDashboard},

        // comment
        {name: 'dashboard_all_comment', path: 'comment', component: AllCommentDashboard}

      ]
    }
  ]
})

window.onload = function () {
  // router.push({name : "summary"})
  new Vue({
    router,
    render: h => h(App)
  }).$mount('#app')
}
