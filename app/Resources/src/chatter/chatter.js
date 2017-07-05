import Token from '../entity/token'
import ChatMessage from '../entity/chatMessage'

export default class Chatter {
  _socket: WebSocket
  _isAuthenticated: false

  constructor (uri: string) {
    this._socket = new WebSocket(uri)
    this.onMessage.then((response) => {
      if (response.origin === 'auth' && response.code === 200) {
        this._isAuthenticated = true
      }
    })
  }

  authenticate (token: Token) {
    this._socket.send(JSON.stringify({
      type: 'auth',
      token: token.token
    }))
  }

  get onMessage () {
    const _this = this
    return new Promise(
      (resolve, rejected) => {
        _this._socket.addEventListener('message', function (event) {
          try {
            const result = JSON.parse(event.data)
            resolve(new ChatMessage(result))
          } catch (e) {
            rejected(e)
          }
        })

        _this._socket.addEventListener('onerror', function (event) {
          rejected(event)
        })
      })
  }

  sendMessage (message: string) {
    this._socket.send(JSON.stringify({
      type: 'message',
      'message': message
    }))
  }
}

