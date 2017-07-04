import BaseEntity from './baseEntity'
export default class FormView extends BaseEntity {
  _form: {[key: string]: {value: any}} = {}

  constructor (data: any) {
    super()
    if ('children' in data) {
      const c = data.children
      for (const k in c) {
        this._form[k] = { value: c[k].vars.value }
      }
    }
  }

  get form () {
    return this._form
  }

  hasValue (key: string) {
    return key in this._value
  }
}
