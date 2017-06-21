import BaseEntity from './baseEntity'
export default class Datatable<T> extends BaseEntity {
  constructor (create : (result: Object) => T, data: {}) {
    super()
    this._sort = this.get('sort', data, [])
    if (Array.isArray(data.payload)) {
      this._payload = data.payload.map((v) => create(v))
    } else {
      this._payload = create(data.payload)
    }
  }

  getPayload () {
    return this._payload
  }

  getSort () {
    return this._sort
  }

  getColumnSort (column) {
    return this._sort[column]
  }
}
