/* @flow */
/* global Routing */
import axios from 'axios'
import ScheduleEntry from './../entity/scheduleEntry'
import Envelope from './../entity/envelope'
import Util from './util'

export default {
  getTodaySchedule (responseCallback: (result: Envelope<ScheduleEntry>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_schedule_today')).then((response) => {
      responseCallback(new Envelope((data) => new ScheduleEntry(data), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getTodayDate (responseCallback: (result: Envelope<string>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_schedule_time')).then((response) => {
      responseCallback(new Envelope((data) => data, response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  },
  getCurrentDateTime (year: number, month: number, day: number, responseCallback: (result: Envelope<Date>) => void, errorResponseCallback: (result: Envelope) => void) {
    axios.get(Routing.generate('get_schedule_by_date', { year: year, month: month, day: day })).then((response) => {
      responseCallback(new Envelope((data) => data.map((r) => new ScheduleEntry(r)), response.data))
    }).catch((error) => {
      Util.handleErrorResponse(error, errorResponseCallback)
    })
  }
}
