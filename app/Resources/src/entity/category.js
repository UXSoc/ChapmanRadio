import BaseEntity from './baseEntity'

export default class Category extends BaseEntity {
  _category: string
  constructor (data: string) {
    super()
    this._category = data
  }
  get category () {
    return this._category
  }
  set category (value) {
    this._category = value
  }
}
