export default class BaseEntity {

  get (key, prop, value = null) {
    if (key in prop) { return prop[key] } else { return value }
  }
}
