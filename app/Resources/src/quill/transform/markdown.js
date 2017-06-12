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
  header: (attr, insert, extra) => {
    return '#'.repeat(attr.header) + ' ' + insert
  },
  blockquote: (attr, insert, extra) => {
    return '> ' + insert
  },
  list: (attr, insert, extra) => {
    switch (attr.list) {
      case 'bullet':
        extra.ordered = 0
        return '* ' + insert
      case 'ordered':
        extra.ordered++
        return extra.ordered + '.' + insert
    }
  }
}

const getLastLine = lines => lines.length > 0 ? lines.pop() : ''

function markdownfromDelta (delta : Delta) {
  let block = false
  let lines:[string] = []
  let extra: {ordered: 0} = {ordered: 0}
  delta.forEach((op) => {
    let attr = op.attributes
    let insert = op.insert
    if (insert.image) {
      insert = '![](' + insert.image + ')'
    }

    if (attr) {
      for (let key in attr) {
        if (attr.hasOwnProperty(key) && key !== null) {
          if (key in _preInline) {
            insert = _preInline[key](attr, insert)
          }
        }
      }

      for (let key in attr) {
        if (attr.hasOwnProperty(key) && key !== null) {
          if (key in _inline) {
            insert = _inline[key](attr, insert)
          }
        }
      }

      for (let key in attr) {
        if (attr.hasOwnProperty(key) && key !== null) {
          if (key in _block) {
            if (insert === '\n') { insert = getLastLine(lines) }
            block = true
            insert = _block[key](attr, insert, extra)
          }
        }
      }

      if (block) {
        insert = '\n' + insert
      }
    }
    let result = (getLastLine(lines) + insert).split('\n')
    for (let index in result) {
      lines.push(result[index])
    }
    if (block === true) {
      lines.push(getLastLine(lines) + '\n')
    }
    if (result.length > 2 || (block === true && !attr.list)) { extra.ordered = 0 }
    block = false
  })
  return lines.join('\n')
}

export {markdownfromDelta}

