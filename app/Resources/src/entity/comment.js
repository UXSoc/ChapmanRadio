import BaseEntity from './baseEntity'
import User from './user'

export default class Comment extends BaseEntity {
  _token: string
  _createdAt: Object
  _content: Object
  _user: Object
  _children: [Comment]

  constructor (data) {
    super()
    this._token = this.get('token', data, '')
    this._createdAt = this.get('created_at', data, {})
    this._content = this.get('content', data, '')
    this._user = this.getAndInstance((data) => new User(data), 'user', data, new User({}))
    this._children = []
    if (data.children_comment) {
      for (let i = 0; i < data.children_comment.length; i++) {
        this._children.push(new Comment(data.children_comment[i]))
      }
    }
  }

  get children (): [Comment] {
    return this._children
  }

  set children (value: [Comment]) {
    this._children = value
  }

  unshift (comment: Comment) {
    this._children.unshift(comment)
  }

  push (comment: Comment) {
    this._children.push(comment)
  }

  set content (content: string) {
    this._content = content
  }

  get content (): string {
    return this._content
  }

  get user (): Object {
    return this._user
  }

  get createdAt (): Object {
    return this._createdAt
  }

  get token (): string {
    return this._token
  }
}
