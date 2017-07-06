import BaseEntity from '../../entity/baseEntity'
export default class Packet extends BaseEntity {
  _type: string
  constructor (data: any) {
    super()
    this._type = this.get('type', data, '')
  }

  get type () {
    return this._type
  }
}
Packet.MESSAGE = 'MESSAGE'
Packet.USERNOTICE = 'USERNOTICE'
Packet.EXCEPTION = 'EXCEPTION'
Packet.AUTH = 'AUTH'

