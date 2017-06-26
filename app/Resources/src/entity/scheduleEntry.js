import BaseEntity from './baseEntity'

import Show from './show'

export default class ScheduleEntry extends BaseEntity {
  _show: Show;
  _date: string
  _length: string

  constructor (data) {
    super()
    this._show = this.getAndInstance((data) => new Show(data), 'show', data, new Show({}))
    this._date = this.get('show_date', data, '')
    this._length = this.get('length', data, '')
  }

  get show () {
    return this._show
  }

  get date () {
    return this._date
  }

  getEndTime () {
    return this._endTime
  }
}
