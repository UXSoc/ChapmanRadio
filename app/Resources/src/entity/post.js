import BaseEntity from './baseEntity'

export default class Post extends BaseEntity {
  constructor (data) {
    super()
    this.categories = this.get('categories', data, '')
    this.tags = this.get('tags', data, '')
    this.token = this.get('token', data, '')
    this.name = this.get('name', data, '')
    this.slug = this.get('slug', data, '')
    this.created_at = this.get('created_at', data, {})
    this.updated_at = this.get('updated_at', data, {})
    this.content = this.get('content', data, '')
    this.token = this.get('token', data, '')
  }

  getCategories () {
    return this.categories
  }

  getTags () {
    return this.tags
  }

  getToken () {
    return this.token
  }

  getName () {
    return this.name
  }

  getSlug () {
    return this.slug
  }

  getCreatedAt () {
    return this.created_at
  }

  getUpdatedAt () {
    return this.updated_at
  }

  getContent () {
    return this.content
  }

  getRoute () {
    return {
      name: 'post_single', params: {token: this.getToken(), slug: this.getSlug()}
    }
  }
}
