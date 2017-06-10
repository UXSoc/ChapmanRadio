import BaseEntity from './baseEntity'

export default class User extends BaseEntity {
  _username: string
  _roles: [string]
  _token: string
  _updatedAt: string
  _createdAt: string

  constructor (data) {
    super()
    this._username = this.get('username', data, '')
    this._roles = this.get('roles', data, ['ROLE_ANONYMOUS'])
    this._token = this.get('token', data, '')
    this._updatedAt = this.get('updated_at', data, '')
    this._createdAt = this.get('created_at', data, '')
  }

  setUsername (test) {
    this._username = test
  }

  getUsername () {
    return this._username
  }

  getRoles () {
    return this._roles
  }

  getToken () {
    return this._token
  }

  getUpdatedAt () {
    return this._updatedAt
  }

  getCreatedAt () {
    return this._createdAt
  }
}
