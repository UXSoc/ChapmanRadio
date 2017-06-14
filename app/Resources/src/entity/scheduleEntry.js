import BaseEntity from './baseEntity'

import Show from './show'

export default class ScheduleEntry extends BaseEntity {
  _show: Show;
  _date: string
  _startTime: string
  _endTime: string

  constructor (data) {
    super()
    this._show = this.getAndInstance((data) => new Show(data), 'show', data, new Show({}))
    this._date = this.get('date', data, '')
    this._startTime = this.getAndInstance('start_time', data, '')
    this._endTime = this.getAndInstance('end_time', data, '')
  }

  getShow () {
    return this._show
  }

  getDate () {
    return this._date
  }

  getStartTime () {
    return this._startTime
  }

  getEndTime () {
    return this._endTime
  }

}
