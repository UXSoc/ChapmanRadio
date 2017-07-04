import BaseEntity from './baseEntity'
export default class ChatMessage extends BaseEntity {
  _message: string

  constructor (data: {}) {
    super()
    this._message = this.get('message', data, '')
  }

  get message () {
    return this._message
  }
}
