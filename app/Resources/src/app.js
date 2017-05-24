import './vendor'
import App from './App.vue'

import Dashboard from './page/dashboard/Dashboard.vue'

// -------------------------------DASHBOARD----------------------------------------------
import AllCommentDashboard from './page/dashboard/comment/AllCommentDashboard.vue'

import AllPostDashboard from './page/dashboard/post/AllPostDashboard.vue'
import AddNewPostDashboard from './page/dashboard/post/AddNewPostDashboard.vue'
import PostCategoryDashboard from './page/dashboard/post/PostCategoryDashboard.vue'
import PostTagDashboard from './page/dashboard/post/PostTagDashboard.vue'

import ProfileDashboard from './page/dashboard/profile/ProfileDashboard.vue'
import ProfileSettingsDashboard from './page/dashboard/profile/ProfileSettingDashboard.vue'

import SettingDashboard from './page/dashboard/setting/SettingDashboard.vue'

import AllShowDashboard from './page/dashboard/show/AllShowDashboard.vue'
import AddNewShowDashboard from './page/dashboard/show/AddNewShowDashboard.vue'
import ShowCategoryDashboard from './page/dashboard/show/ShowCategoryDashboard.vue'
import ShowTagDashboard from './page/dashboard/show/ShowTagDashboard.vue'
import ShowDashboard from './page/dashboard/show/ShowDashboard.vue'

import AddNewUserDashboard from './page/dashboard/user/AddNewUserDashboard.vue'
import AllUserDashboard from './page/dashboard/user/AllUserDashboard.vue'
import UserDashboard from './page/dashboard/user/UserDashboard.vue'
// -------------------------------App----------------------------------------------

import AppPage from './page/app/AppPage.vue'
import LoginPage from './page/app/LoginPage.vue'
import RegisterPage from './page/app/RegisterPage.vue'

import Vue from 'vue'
import VueRouter from 'vue-router'

const router = new VueRouter({
  routes: [
    {
      name: 'index', path: '/', component: AppPage,
      children: [
        {name: 'login', path: 'login', component: LoginPage},
        {name: 'register', path: 'register', component: RegisterPage}
      ]
    },
    // -------------------------------DASHBOARD----------------------------------------------
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
    // -------------------------------App----------------------------------------------
  ]
})

window.onload = function () {
  // router.push({name : "summary"})
  new Vue({
    router,
    render: h => h(App)
  }).$mount('#app')
}
