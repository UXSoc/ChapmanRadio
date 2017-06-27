import BaseEntity from './baseEntity'
export default class Pagination<T> extends BaseEntity {
  _page: number
  _perPage: number
  _count: number
  _result: [T]

  constructor (create : (result: Object) => T, data: {}) {
    super()
    this._page = this.get('pages', data, 0)
    this._perPage = this.get('perPage', data, 0)
    this._count = this.get('count', data, 0)
    this._result = data.result.map((v) => create(v))
  }

  get maxPage () {
    if (this._count === this._perPage) {
      return 0
    }
    return Math.ceil(this._count / this._perPage)
  }

  get nextPage () {
    return this._page + 1
  }

  get previousPage () {
    return this._page - 1
  }

  get currentPage () {
    return this._page
  }

  get result () {
    return this._result
  }
}

