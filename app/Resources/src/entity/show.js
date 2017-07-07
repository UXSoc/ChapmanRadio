import BaseEntity from './baseEntity'

export default class Show extends BaseEntity {
  _createdAt: string
  _updatedAt: string
  _tags: [string]
  _description: string
  _genres: [string]
  _name: string
  _profanity: boolean
  _token: string
  _slug: string
  _excerpt: string
  _headerImage: string
  _enableComments: boolean
  _djs: [Object]

  constructor (data) {
    super()
    this._createdAt = this.get('created_at', data, {})
    this._updatedAt = this.get('updated_at', data, {})
    this._tags = this.get('tags', data, '')
    this._description = this.get('description', data, '')
    this._genres = this.get('genres', data, [])
    this._name = this.get('name', data, '')
    this._profanity = this.get('profanity', data, false)
    this._token = this.get('token', data, '---------')
    this._slug = this.get('slug', data, '')
    this._excerpt = this.get('excerpt', data, '')
    this._headerImage = this.get('header_image', data, '')
    this._enableComments = this.get('enable_comments', data, '')
    this._djs = this.get('djs', data, [])
    this._strikes = this.get('strikes', data, null)
  }

  get dj () : [Object] {
    return this._djs
  }

  get tags () : [string] {
    return this._tags
  }

  get headerImage () : string {
    return this._headerImage
  }

  get hasProfanity () : boolean {
    return this._profanity
  }

  set hasProfanity (value: boolean) {
    this._profanity = value
  }

  get commentsEnabled () : boolean {
    return this._enableComments
  }

  set commentsEnabled (value: boolean) {
    this._enableComments = value
  }

  get genres () : [string] {
    return this._genres
  }

  set genres (value: [string]) {
    this._genres = value
  }

  get profanity () : boolean {
    return this._profanity
  }

  set profanity (value: boolean) {
    this._profanity = value
  }

  get excerpt () : string {
    return this._excerpt
  }

  set excerpt (value: string) {
    this._excerpt = value
  }

  get description () : string {
    return this._description
  }

  set description (value: string) {
    this._description = value
  }

  get token (): string {
    return this._token
  }

  get name () : string {
    return this._name
  }

  set name (value: string) {
    this._name = value
  }

  get slug () : string {
    return this._slug
  }

  set slug (value: string) {
    this._slug = value.replace(/(-|\s|\n)+/g, '-')
  }

  get createdAt () : string {
    return this._createdAt
  }

  get updatedAt () : string {
    return this._updatedAt
  }

  get strikes () : string {
    return this._strikes
  }

  get payload () {
    return {
      show: {
        name: this.name,
        description: this.description,
        excerpt: this.excerpt,
        slug: this.slug,
        tags: this.tags,
        genres: this.genres
      }
    }
  }

  getRoute () : {name: string, params: { token: string, slug:string}} {
    return {
      name: 'show_single', params: { token: this.token, slug: this.slug }
    }
  }

  getRouteToEdit () : {name: string, params: { token: string, slug:string}} {
    return {
      name: 'dashboard_show_edit', params: { token: this.token, slug: this.slug }
    }
  }
}
