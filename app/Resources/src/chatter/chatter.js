import Token from '../entity/token'
import Message from './packets/message'
import UserNotice from './packets/userNotice'
import Packet from './packets/packet'
export default class Chatter {
  _socket: WebSocket
  _isAuthenticated: false

  constructor (uri: string) {
    this._socket = new WebSocket(uri)
    this.setMessageCallback((response) => {
      if (response instanceof UserNotice) {
        if (response.flag === UserNotice.VERIFIED) {
          this._isAuthenticated = true
        }
      }
    }, (e) => {

    })
  }

  authenticate (token: Token) {
    this._socket.send(JSON.stringify({
      type: Packet.AUTH,
      token: token.token
    }))
  }

  setMessageCallback (callback, reject) {
    const _this = this
    _this._socket.addEventListener('message', function (event) {
      try {
        const result = JSON.parse(event.data)
        switch (result.type) {
          case Packet.USERNOTICE:
            callback(new UserNotice(result))
            break
          case Packet.MESSAGE:
            callback(new Message(result))
            break
        }
      } catch (e) {
        reject(e)
      }
    })
  }

  get isAuthenticated () {
    return this._isAuthenticated
  }

  sendMessage (message: string) {
    this._socket.send(JSON.stringify({
      type: Packet.MESSAGE,
      'message': message
    }))
  }
}

