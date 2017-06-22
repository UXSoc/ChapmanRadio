import BaseEntity from './baseEntity'

export default class Tag extends BaseEntity {
  static createTag (tag) {
    return new Tag(tag)
  }

  _tag: string
  constructor (data) {
    super()
    this._tag = data
  }

  setTag (tag: string) {
    this._tag = tag
  }

  getTag () {
    return this._tag
  }
}
