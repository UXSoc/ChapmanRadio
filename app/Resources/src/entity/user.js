/* global Routing */
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

  isLoggedIn () {
    return !this._roles.includes('ROLE_ANONYMOUS')
  }

  isDj () {
    return this._roles.includes('ROLE_DJ')
  }

  isStaff () {
    return this._roles.includes('ROLE_STAFF')
  }

  hasRole (role) {
    return this._roles.includes(role)
  }

  set username (value) {
    this._username = value
  }

  get username () {
    return this._username
  }

  get roles () {
    return this._roles
  }

  get token () {
    return this._token
  }

  get updatedAt () {
    return this._updatedAt
  }

  get createdAt () {
    return this._createdAt
  }

  getProfileImage () {
    return Routing.generate('get_profile_image', { token: this.token })
  }
}
