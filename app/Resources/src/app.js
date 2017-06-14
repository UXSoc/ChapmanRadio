import './vendor'
import App from './App.vue'

import Dashboard from './page/dashboard/Dashboard.vue'

// -------------------------------DASHBOARD----------------------------------------------
import BroadcastLiveDashboard from './page/dashboard/dj/BroadcastLiveDashboard.vue'
import GradesAttendanceDashboard from './page/dashboard/dj/GradesAttendanceDashboard.vue'
import MyShowsDashboard from './page/dashboard/dj/MyShowsDashboard.vue'
import SettingsDashboard from './page/dashboard/dj/SettingsDashboard.vue'

import AttendanceDashboard from './page/dashboard/siteadmin/AttendanceDashboard.vue'
import BlogDashboard from './page/dashboard/siteadmin/BlogDashboard.vue'
import EmailAlertsDashboard from './page/dashboard/siteadmin/EmailAlertsDashboard.vue'
import GradeMgmtDashboard from './page/dashboard/siteadmin/GradeMgmtDashboard.vue'
import ScheduleDashboard from './page/dashboard/siteadmin/ScheduleDashboard.vue'
import ShowsDashboard from './page/dashboard/siteadmin/ShowsDashboard.vue'
import StaffDashboard from './page/dashboard/siteadmin/StaffDashboard.vue'
import StrikesDashboard from './page/dashboard/siteadmin/StrikesDashboard.vue'
import UsersDashboard from './page/dashboard/siteadmin/UsersDashboard.vue'
// -------------------------------App----------------------------------------------

import AppPage from './page/app/AppPage.vue'
import LoginPage from './page/app/LoginPage.vue'
import RegisterPage from './page/app/RegisterPage.vue'
import PostPage from './page/app/Blog/PostPage.vue'
import PostSingle from './page/app/Blog/PostSingle.vue'
import PostList from './page/app/Blog/PostList.vue'
import ShowSingle from './page/app/Show/ShowSingle.vue'
import ContactPage from './page/app/ContactPage.vue'
import EventPage from './page/app/Event/EventPage.vue'
import SchedulePage from './page/app/Schedule/SchedulePage.vue'
import ShowPage from './page/app/Show/ShowPage.vue'
import HomePage from './page/app/HomePage.vue'
import ScheduleList from './page/app/Schedule/ScheduleList.vue'
import ScheduleSingle from './page/app/Schedule/ScheduleSingle.vue'
import ShowList from './page/app/Show/ShowList.vue'

import Vue from 'vue'
import VueRouter from 'vue-router'
import Player from './install/player/player'
import Auth from './install/auth/auth'

const auth = new Auth()

const player = new Player()

const router = new VueRouter({ routes: [
  {
    path: '/', component: AppPage,
    children: [
      {name: 'contact', path: 'contact', component: ContactPage},
      {name: 'home', path: '', component: HomePage},
      {name: 'login', path: 'login', component: LoginPage},
      {name: 'register', path: 'register', component: RegisterPage},
      {name: 'event', path: 'event', component: EventPage},
      // Posts --------------------------------------------------------------------
      {path: 'post', component: PostPage, children: [
          {name: 'post', path: '/', component: PostList},
          {name: 'post_single', path: ':token/:slug', component: PostSingle}
      ]},
      // Shows --------------------------------------------------------------------
      {path: 'show', component: ShowPage, children: [
          {name: 'show', path: '/', component: ShowList},
          {name: 'show_single', path: ':token/:slug', component: ShowSingle}
      ]},
      // --------------------------------------------------------------------------
      {path: 'schedule', component: SchedulePage, children: [
        {name: 'schedule', path: '/', component: ScheduleList},
        {name: 'schedule_list', path: ':year/:month/:day', component: ScheduleList},
        {name: 'schedule_single', path: ':token', component: ScheduleSingle}
      ]}
    ]
  },
  // -------------------------------DASHBOARD----------------------------------------------
  {
    name: 'dashboard', path: '/dashboard/', component: Dashboard,
    children: [
        {name: 'dashboard_broadcast_live', path: 'broadcastlive', component: BroadcastLiveDashboard},
        {name: 'dashboard_grades_attendance', path: 'gradesattendance', component: GradesAttendanceDashboard},
        {name: 'dashboard_my_shows', path: 'myshows', component: MyShowsDashboard},
        {name: 'dashboard_settings', path: 'settings', component: SettingsDashboard},

        {name: 'dashboard_attendance', path: 'attendance', component: AttendanceDashboard},
        {name: 'dashboard_blog', path: 'blog', component: BlogDashboard},
        {name: 'dashboard_emailalerts', path: 'emailalerts', component: EmailAlertsDashboard},
        {name: 'dashboard_grade_mgmt', path: 'grademgmt', component: GradeMgmtDashboard},
        {name: 'dashboard_schedule', path: 'schedule', component: ScheduleDashboard},
        {name: 'dashboard_shows', path: 'shows', component: ShowsDashboard},
        {name: 'dashboard_staff', path: 'staff', component: StaffDashboard},
        {name: 'dashboard_strikes', path: 'strikes', component: StrikesDashboard},
        {name: 'dashboard_users', path: 'users', component: UsersDashboard}
    ]
  }
  // -------------------------------App----------------------------------------------
]})

window.onload = function () {
  new Vue({
    auth,
    router,
    player,
    render: h => h(App)
  }).$mount('#app')
}
