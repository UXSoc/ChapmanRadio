import BaseEntity from './baseEntity'
import { ErrorBag } from 'vee-validate'
export default class Envelope extends BaseEntity {
  constructor (create, data) {
    super()
    this._success = data.success
    this._message = data.message
    if (this._success) {
      this._data = create(data.data)
    } else {
      this._errors = []
      for (let i = 0; i < data.errors.length; i++) {
        this._errors[data.errors[i].field] = data.errors[i].message
      }
      this._errors = data.errors
    }
  }

  getResult () {
    return this._data
  }

  getMessage () {
    return this._message
  }

  isSuccess () {
    return this._success
  }

  getErrors () {
    return this._errors
  }

  fillErrorBag(errorBag : ErrorBag)
  {

  }

}
