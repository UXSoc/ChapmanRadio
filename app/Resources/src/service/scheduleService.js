/* @flow */
/* global Routing */
import axios from 'axios'
import ScheduleEntry from './../entity/scheduleEntry'

export default {
  getTodayDate (responseCallback: (result: string) => void) {
    return axios.get(Routing.generate('get_schedule_time')).then((response) => {
      responseCallback(response.data.time)
    })
  },
  getScheduleByDate (year: number, month: number, day: number, callback: (result: [ScheduleEntry]) => void) {
    return axios.get(Routing.generate('get_schedule_by_date', { year: year, month: month, day: day })).then((response) => {
      callback(response.data.scheduleEntries.map((r) => new ScheduleEntry(r)))
    })
  }
}
