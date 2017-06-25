// @flow
declare type Pagination<T> = {
  items: [T],
  page: number,
  count: number,
  perPage: number,
}

declare type Post = {
  name : string,
  token: string,
  slug: string,
  created_at: string,
  updated_at: string,
  excerpt: string,
  tags: [string],
  categories: [string],
  comments: []
}