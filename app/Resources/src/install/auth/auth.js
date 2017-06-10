
import { install } from './install'
import User from './../../entity/user'
import AuthService from './../../service/authService'

export default class Auth {
  static install: () => void
  status: User
  _app: any
  _apps: [any]

  constructor () {
    this._app = null
    this._apps = []
    this.status = new User({})
    let _this = this
    AuthService.getStatus((envelope) => {
      _this.status = envelope.getResult()
    }, (envelope) => {
      _this.status = new User({})
    })
  }

  init (app: any) {
    this._apps.push(app)
    if (this.app) {
      return
    }

    this._app = app

    this._apps.forEach((app) => {
      app._player = this
    })
  }

  getStatus () {
    return this.status
  }

  refresh () {
    this.status = new User({'username': 'derp'})
    // this.updateUser()
  }

  // updateUser () {
  //   this._app.$root._status = this._status
  // }
}

Auth.install = install
window.Vue.use(Auth)
