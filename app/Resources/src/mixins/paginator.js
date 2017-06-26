// @flow
export default {
  methods: {
    maxPage: function (count: number, perPage: number) {
      if (count === perPage) {
        return 0
      }
      return Math.ceil(count / perPage)
    }
  }
}
