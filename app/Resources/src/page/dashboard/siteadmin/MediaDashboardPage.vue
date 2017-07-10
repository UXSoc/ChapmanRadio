<template>
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#new-media" aria-controls="newMedia" role="tab" data-toggle="tab">New Media</a>
            </li>
            <li role="presentation">
                <a href="#library" aria-controls="library" role="tab" data-toggle="tab">Library</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="newMedia">
                <media-form></media-form>
            </div>
            <div role="tabpanel" class="tab-pane" id="library">
                library
            </div>

        </div>

    </div>
</template>

<script>
    import MediaForm from './../../../form/MediaForm.vue'
    /* @flow */
    export default{
      data () {
        return {
        }
      },
      methods: {

      },
      watch: {
      },
      components: {
        MediaForm
      }
    }
</script>