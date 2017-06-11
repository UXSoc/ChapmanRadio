import Delta from 'quill-delta/lib/delta'

const _preInline: [] = {
  link: (attr, insert) => {
    return '[' + attr.link + '](' + insert + ')'
  }
}

const _inline: [] = {
  italic: (attr, insert) => {
    return '*' + insert + '*'
  },
  bold: (attr, insert) => {
    return '**' + insert + '**'
  },
  code: (attr, insert) => {
    return '`' + insert + '`'
  }
}

const _block: [] = {
  header: (attr, insert) => {
    return '#'.repeat(attr.header) + ' ' + insert
  },
  blockquote: (attr, insert) => {
    return '> ' + insert
  }
}

export default class QuillMarkdown {
  // _embed = {
  //
  // }

  fromDelta (delta : Delta) {
    let lines: [string] = []
    delta.forEach((op) => {
      let attr = op.attributes
      if (typeof op.insert === 'string') {
        if (attr) {
          if (op.insert === '\n' && op.length === 1) {
            let result = this._getLastLine(lines)
            for (let key in attr) {
              if (attr.hasOwnProperty(key) && key !== null) {
                if (key in _block) {
                  result = _block[attr[key]](attr, result)
                }
              }
            }
            lines.push(result)
          } else {
            let result = op.insert
            for (let key in attr) {
              console.log(key)
            }
            for (let key in attr) {
              if (attr.hasOwnProperty(key) && key !== null) {
                if (key in _preInline) {
                  result = _preInline[key](attr, result)
                }
              }
            }

            for (let key in attr) {
              if (attr.hasOwnProperty(key) && key !== null) {
                if (key in _inline) {
                  result = _inline[key](attr, result)
                }
              }
            }

            lines.push(this._getLastLine(lines) + result)
          }
        } else {
          let result = (this._getLastLine(lines) + op.insert).split('\n')
          for (let index in result) {
            lines.push(result[index])
          }
        }
      } else {

      }
    })
    return lines.join('\n')
  }

  _getLastLine (lines) {
    if (lines.length > 0) { return lines.pop() }
    return ''
  }

}

