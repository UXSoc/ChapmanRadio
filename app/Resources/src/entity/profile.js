import BaseEntity from './baseEntity'
import Image from './image'

export default class Profile extends BaseEntity {
  _phone: string
  _image: Image

  constructor (data) {
    super()
    this._phone = this.get('phone', data, '')
    this._image = this.getAndInstance((data) => new Image(data), 'image', data, new Image({}))
  }

  get phone () {
    return this._phone
  }

  get image () {
    return this._image
  }
}
