import BaseEntity from './baseEntity'
import Image from './image'

export default class Profile extends BaseEntity {
  _firstName: string
  _lastName: string
  _image: Image

  constructor (data) {
    super()
    this._firstName = this.get('first_name', data, '')
    this._lastName = this.get('last_name', data, '')
    this._image = this.getAndInstance((data) => new Image(data), 'image', data, new Image({}))
  }

  get firstName () {
    return this._firstName
  }

  set firstName (value) {
    this._firstName = value
  }

  set lastName (value) {
    this._lastName = value
  }

  get lastName () {
    return this._lastName
  }

  get phone () {
    return this._phone
  }

  get image () {
    return this._image
  }
}
