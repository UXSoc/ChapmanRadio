import BaseEntity from './baseEntity'
import { ErrorBag } from 'vee-validate'
export default class Form extends BaseEntity {
  _code: string
  _message: string

  constructor (data: any) {
    super()
    this._code = this.get('code', data, 0)
    this._message = this.get('message', data, '')
    if ('errors' in data) {
      this._errors = this.get('children', data.errors, [])
    }
  }

  getErrors (error: string) {
    const e = this._errors[error]
    if ('errors' in e) { return e.errors }
    return null
  }

  FillErrobag (errorBag: ErrorBag, alias: any) {
    for (const key in this._errors) {
      let k = key
      if (key in alias) { k = alias[key] }
      const errors = this.getErrors(key)
      if (errors) {
        errorBag.add(k, errors[0])
      }
    }
  }

  get message () {
    return this._message
  }

  get code () {
    return this._code
  }
}

