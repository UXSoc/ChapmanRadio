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

  getDjs () : [Object] {
    return this._djs
  }

  getTags () : [string] {
    return this._tags
  }

  getHeaderImage () : string {
    return this._headerImage
  }

  hasProfanity () : boolean {
    return this._profanity
  }

  hasCommentsEnabled () : boolean {
    return this._enableComments
  }

  getGenres () : [string] {
    return this._genres
  }

  getProfanity () : boolean {
    return this._profanity
  }

  getExcerpt () : string {
    return this._excerpt
  }

  getDescription () : string {
    return this._description
  }

  getToken (): string {
    return this._token
  }

  getName () : string {
    return this._name
  }

  getSlug () : string {
    return this._slug
  }

  getCreatedAt () : string {
    return this._createdAt
  }

  getUpdatedAt () : string {
    return this._updatedAt
  }

  getContent () :string {
    return this._content
  }

  getStrikes () : string {
    return this._strikes
  }

  getRoute () : {name: string, params: { token: string, slug:string}} {
    return {
      name: 'show_single', params: { token: this.getToken(), slug: this.getSlug() }
    }
  }
}
