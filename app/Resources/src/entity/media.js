import BaseEntity from './baseEntity'
export default class Media extends BaseEntity {
  _createdAt: string
  _updatedAt: string
  _title: string
  _altText: string
  _description: string
  _mime: string
  _token: string
  _path: string
  _caption: string
  _file: File

  constructor (data) {
    super()
    this._createdAt = this.get('created_at', data, '')
    this._updatedAt = this.get('updated_at', data, '')
    this._title = this.get('title', data, '')
    this._altText = this.get('alt_text', data, '')
    this._description = this.get('description', data, '')
    this._mime = this.get('mime', data, '')
    this._token = this.get('token', data, '')
    this._path = this.get('path', data, '')
    this._caption = this.get('caption', data, '')
  }

  set file (value) {
    this._file = value
  }

  get file () {
    return this._file
  }

  set caption (value) {
    this._caption = value
  }

  get caption () {
    return this._caption
  }

  get updatedAt () {
    return this._updatedAt
  }

  get createdAt () {
    return this._createdAt
  }

  get description () {
    return this._description
  }

  set description (value) {
    this._description = value
  }

  get altText () {
    return this._altText
  }

  set altText (value) {
    this._altText = value
  }

  get title () {
    return this._title
  }

  set title (value) {
    this._title = value
  }

  get mime () {
    return this._mime
  }

  get token () {
    return this._token
  }

  get path () {
    return this._path
  }
}
