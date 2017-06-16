import BaseEntity from './baseEntity'
import { ErrorBag } from 'vee-validate'
export default class Envelope<T> extends BaseEntity {
  _success: string
  _message: string
  _errors: {[string]: string}
  _data: T

  constructor (create : (result: Object) => T, data) {
    super()
    this._success = data.success
    this._message = data.message
    if (this._success) {
      this._data = create(data.data)
    } else {
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

  fillErrorBag (errorBag : ErrorBag) {
    for (let key in this._errors) {
      if (typeof this._errors[key] === 'string') {
        errorBag.add(key, this._errors[key])
      }
    }
  }

}
