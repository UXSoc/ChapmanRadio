import BaseEntity from './baseEntity'

export default class Post extends BaseEntity {
    _token: string

    constructor (data) {
    super()
    this._token = this.get('token', data, [])
  }

    get token () {
    return this._token
  }
}
