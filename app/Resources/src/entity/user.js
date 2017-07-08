import BaseEntity from './baseEntity'
import Profile from './profile'
export default class User extends BaseEntity {
  _username: string
  _roles: [string]
  _token: string
  _updatedAt: string
  _createdAt: string
  _profile: Profile

  constructor (data) {
    super()
    this._username = this.get('username', data, '')
    this._roles = this.get('roles', data, ['ROLE_ANONYMOUS'])
    this._token = this.get('token', data, '')
    this._updatedAt = this.get('updated_at', data, '')
    this._createdAt = this.get('created_at', data, '')
    this._profile = this.getAndInstance((data) => new Profile(data), 'profile', data, new Profile({}))
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

  get profile () {
    return this._profile
  }

  getProfileImage () {
    return this.profile.image.path
  }
}
