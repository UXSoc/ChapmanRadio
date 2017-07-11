import BaseEntity from './baseEntity'

export default class Post extends BaseEntity {
  _categories: string
  _tags: [string]
  _token: string
  _name: string
  _slug: string
  _createdAt: Object
  _updatedAt: Object
  _content: string
  _excerpt: string
  _isPinned: boolean

  constructor (data) {
    super()
    this._categories = this.get('categories', data, [])
    this._tags = this.get('tags', data, [])
    this._token = this.get('token', data, '84nadjhankn')
    this._name = this.get('name', data, '')
    this._slug = this.get('slug', data, '')
    this._createdAt = this.get('created_at', data, '--')
    this._updatedAt = this.get('updated_at', data, '--')
    this._content = this.get('content', data, '{"ops": []}')
    this._excerpt = this.get('excerpt', data, '')
    this._isPinned = this.get('isPinned', data, false)
  }

  get isPinned () {
    return this._isPinned
  }

  set isPinned (value) {
    this._isPinned = value
  }

  get excerpt () {
    return this._excerpt
  }

  set excerpt (value) {
    this._excerpt = value
  }

  get categories () {
    return this._categories
  }

  set categories (value) {
    this._categories = value
  }

  get tags () {
    return this._tags
  }

  set tags (value) {
    this._tags = value
  }

  get token () {
    return this._token
  }

  get name () {
    return this._name
  }

  set name (value) {
    this._name = value
  }

  get slug () {
    return this._slug
  }

  set slug (value) {
    this._slug = value.replace(/(-|\s|\n)+/g, '-')
  }

  get content () {
    return this._content
  }

  set content (value) {
    this._content = value
  }

  get createdAt () {
    return this._createdAt
  }

  get updatedAt () {
    return this._updatedAt
  }

  getRoute () {
    return {
      name: 'post_single', params: { token: this.token, slug: this.slug }
    }
  }

  getRouteToEdit () {
    return {
      name: 'dashboard_blog_edit', params: { token: this.token, slug: this.slug }
    }
  }
}
