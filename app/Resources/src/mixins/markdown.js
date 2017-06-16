import commonmark from 'commonmark'

export default {
  filters: {
    markdown: function (value) {
      let reader = new commonmark.Parser()
      let writer = new commonmark.HtmlRenderer({safe: true})
      let parsed = reader.parse(value)

      return writer.render(parsed)
    }
  }
}
