import BaseEntity from './baseEntity'
export default class Image extends BaseEntity {
  _createdAt: string
  _path: string

  constructor (data) {
    super()
    this._createdAt = this.get('created_at', data, '')
    this._path = this.get('path', data, '')
  }

  get createdAt () {
    return this._createdAt
  }

  get path () {
    return this._path
  }
}
