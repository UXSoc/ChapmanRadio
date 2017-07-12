import BaseEntity from './baseEntity'
import Profile from './profile'
export default class User extends BaseEntity {
  _username: string
  _roles: [string]
  _token: string
  _updatedAt: string
  _createdAt: string
  _profile: Profile
  _password: string
  _studentId: string
  _email: string

  constructor (data) {
    super()
    this._username = this.get('username', data, '')
    this._roles = this.get('roles', data, ['ROLE_ANONYMOUS'])
    this._studentId = this.get('student_Id', data, '')
    this._email = this.get('email', data, '')
    this._token = this.get('token', data, '')
    this._updatedAt = this.get('updated_at', data, '')
    this._createdAt = this.get('created_at', data, '')
    this._profile = this.getAndInstance((data) => new Profile(data), 'profile', data, new Profile({}))
    this._password = ''
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

  set password (value) {
    this._password = value
  }

  get password () {
    return this._password
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

  get email () {
    return this._email
  }

  set email (value) {
    this._email = value
  }

  get studentId () {
    return this._studentId
  }

  set studentId (value) {
    this._studentId = value
  }

  getProfileImage () {
    return this.profile.image.uri
  }
}
