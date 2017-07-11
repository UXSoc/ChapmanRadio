import BaseEntity from './baseEntity'
export default class Image extends BaseEntity {
  _createdAt: string
  _uri: string

  constructor (data) {
    super()
    this._createdAt = this.get('created_at', data, '')
    this._uri = this.get('uri', data, '')
  }

  get createdAt () {
    return this._createdAt
  }

  get uri () {
    return this._uri
  }
}
