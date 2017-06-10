import BaseEntity from './baseEntity'
export default class Pagination extends BaseEntity {
  constructor (create, data) {
    super()
    this._page = this.get('pages', data, 0)
    this._perPage = this.get('perPage', data, 0)
    this._count = this.get('count', data, 0)
    this._result = data.result.map((v) => create(v))
  }

  getMaxPage () {
    return Math.ceil(this._count / this._perPage)
  }

  getNextPage () {
    return this._page + 1
  }

  getPreviousPage () {
    return this._page - 1
  }

  getCurrentPage () {
    return this._page
  }

  getResult () {
    return this._result
  }

}
