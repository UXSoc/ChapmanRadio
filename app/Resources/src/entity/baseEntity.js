export default class BaseEntity {
  get (key: string, prop: any, value: any = null) {
    if (key in prop) { return prop[key] } else { return value }
  }

  getAndInstance<T> (callback: (value: any) => T, key: string, prop : any, value: any) {
    if (key in prop) { return callback(prop[key]) } else { return value }
  }
}
