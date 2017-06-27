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
    this._token = this.get('token', data, '')
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

  get hasCommentsEnabled () : boolean {
    return this._enableComments
  }

  get genres () : [string] {
    return this._genres
  }

  get profanity () : boolean {
    return this._profanity
  }

  get excerpt () : string {
    return this._excerpt
  }

  get description () : string {
    return this._description
  }

  get token (): string {
    return this._token
  }

  get name () : string {
    return this._name
  }

  get slug () : string {
    return this._slug
  }

  get createdAt () : string {
    return this._createdAt
  }

  get updatedAt () : string {
    return this._updatedAt
  }

  get content () :string {
    return this._content
  }

  get strikes () : string {
    return this._strikes
  }

  getRoute () : {name: string, params: { token: string, slug:string}} {
    return {
      name: 'show_single', params: { token: this.token, slug: this.slug }
    }
  }
}
