import Packet from './packet'
import User from '../../entity/user'

export default class Message extends Packet {
  _message: string
  _user: User
  _time: string
  constructor (data: any) {
    super()
    this._user = this.getAndInstance((data) => new User(data), 'user', data, null)
    this._message = this.get('message', data, '')
    this._time = this.get('time', data, null)
  }
}
