
import { install } from './install'
import Track from './track'

export default class Player {
  static install: () => void
  _currentTrack: Track
  _app: any
  _apps: [any]

  constructor () {
    this._app = null
    this._apps = []
    this._currentTrack = new Track()
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

  play () {
    console.log('hello this is playing')
  }
}

Player.install = install
window.Vue.use(Player)
