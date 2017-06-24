// @flow
import commonmark from 'commonmark'
export default {
  filters: {
    markdown: function (value: string) {
      const reader = new commonmark.Parser()
      const writer = new commonmark.HtmlRenderer({ safe: true })
      const parsed = reader.parse(value)

      return writer.render(parsed)
    }
  }
}
