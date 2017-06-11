import Quill from 'quill/core'

import Toolbar from 'quill/modules/toolbar'
import Snow from 'quill/themes/snow'

import Bold from 'quill/formats/bold'
import Italic from 'quill/formats/italic'
import Header from 'quill/formats/header'
import Image from 'quill/formats/image'
import Link from 'quill/formats/link'
import Strike from 'quill/formats/strike'
import List from 'quill/formats/list'
import Underline from 'quill/formats/underline'
import BlockQuote from 'quill/formats/blockquote'
import { AlignClass } from 'quill/formats/align'
import { SizeStyle } from 'quill/formats/size'
import Video from 'quill/formats/video'

Quill.register({
  'modules/toolbar': Toolbar,
  'themes/snow': Snow,
  'formats/align': AlignClass,
  'formats/bold': Bold,
  'formats/italic': Italic,
  'formats/header': Header,
  'formats/image': Image,
  'formats/link': Link,
  'formats/strike': Strike,
  'formats/list': List,
  'formats/underline': Underline,
  'formats/blockquote': BlockQuote,
  'formats/size': SizeStyle,
  'formats/video': Video
})

export default Quill
