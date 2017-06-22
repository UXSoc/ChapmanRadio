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

  get tag () {
    return this._tag
  }
  set tag (value) {
    this._tag = value
  }

}
