import BaseEntity from './baseEntity'
import Tag from './tag'
export default class Post extends BaseEntity {
  _categories: string
  _tags : [string]
  _token: string
  _name: string
  _slug: string
  _createdAt: Object
  _updatedAt: Object
  _content: string
  _excerpt: string

  constructor (data) {
    super()
    this._categories = this.get('categories', data, '')
    this._tags = this.getAndInstance((data) => data.map((t) => new Tag(t)), 'tags', data, '')
    this._token = this.get('token', data, '')
    this._name = this.get('name', data, '')
    this._slug = this.get('slug', data, '')
    this._createdAt = this.get('created_at', data, {})
    this._updatedAt = this.get('updated_at', data, {})
    this._content = this.get('content', data, '')
    this._excerpt = this.get('excerpt', data, '')
  }
  get Excerpt () {
    return this._category
  }

  set Excerpt (value) {
    this._category = value
  }

  getCategories () {
    return this._categories
  }

  getTags () {
    return this._tags
  }

  getToken () {
    return this._token
  }

  getName () {
    return this._name
  }

  getSlug () {
    return this._slug
  }

  getCreatedAt () {
    return this._created_at
  }

  getUpdatedAt () {
    return this._updated_at
  }

  getContent () {
    return this._content
  }

  getRoute () {
    return {
      name: 'post_single', params: {token: this.getToken(), slug: this.getSlug()}
    }
  }

  getRouteToEdit () {
    return {
      name: 'dashboard_blog_edit', params: {token: this.getToken(), slug: this.getSlug()}
    }
  }
}
