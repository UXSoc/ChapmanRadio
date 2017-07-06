import Packet from './packet'

export default class UserNotice extends Packet {
  _message: string
  _flag: string

  constructor (data: any) {
    super(data)
    this._message = this.get('message', data, '')
    this._flag = this.get('flag', data, '')
  }

  get flag () {
    return this._flag
  }

  get message () {
    return this._message
  }
}
UserNotice.VERIFIED = 'VERIFIED'
