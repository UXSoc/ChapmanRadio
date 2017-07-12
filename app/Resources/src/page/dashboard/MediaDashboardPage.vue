<template>
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#newMedia" aria-controls="newMedia" role="tab" data-toggle="tab">New Media</a>
            </li>
            <li role="presentation">
                <a href="#library" aria-controls="library" role="tab" data-toggle="tab">Library</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="newMedia">
                <multi-media-upload-form @upload="uploadMedia"></multi-media-upload-form>
            </div>
            <div role="tabpanel" class="tab-pane" id="library">
                <media-form></media-form>
            </div>

        </div>

    </div>
</template>

<script>
    import MediaForm from '../../form/MediaForm.vue'
    import MultiMediaUploadForm from '../../form/MultiMediaUploadForm.vue'
    import MediaService from '../../service/mediaService'

    /* @flow */
    export default{
      data () {
        return {
        }
      },
      methods: {
        uploadMedia: function (item) {
          MediaService.postMedia(item.media, (result) => {
          })
        }
      },
      watch: {
      },
      components: {
        MediaForm,
        MultiMediaUploadForm
      }
    }
</script>