<template>
    <div>
        <form>
            <label class="file-window file-button">
                Upload <input type="file" v-on:change="uploadWindow" />
            </label>
            <ul>
                <li v-for="(item,index) in items">
                    <img :src="item.file"/>
                </li>
            </ul>

            <button class="btn btn-default" @click="upload" type="button" name="button">Upload</button>
        </form>
    </div>
</template>

<script>
  import Media from './../entity/media'

  export default{
    data () {
      return {
        items: []
      }
    },
    props: {
    },
    methods: {
      uploadWindow: function (e) {
        const files = e.target.files || e.dataTransfer.files
        if (!files.length) {
          return
        }
        for (let i = 0; i < files.length; i++) {
          const _this = this
          const reader = new FileReader()
          const file = files[i]
          reader.onload = function (e) {
            const media = new Media({})
            media.file = file
            _this.items.push({
              media: media,
              file: e.target.result,
              progress: 0,
              status: 'waiting'
            })
          }
          reader.readAsDataURL(file)
        }
      },
      upload: function () {
        for (let i = 0; i < this.items.length; i++) {
          if (this.items[i].status === 'waiting') {
            this.$emit('upload', this.items[i])
          }
        }
      }
    }
  }
</script>