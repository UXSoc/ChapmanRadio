<template>
    <form>

        <img :src="userImage">
        <label class="btn btn-default btn-file file-button">
            Upload Image <input type="file" v-on:change="profileImage" />
        </label>

        <div class="modal fade" tabindex="-1" role="dialog" id="profile-image-edit-modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            <img id="profile-image-edit">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-on:click.prevent="save">Save changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </form>
</template>

<script>
  import Cropper from 'cropperjs'
  import FormGroup from './../components/FormGroup.vue'
  import Alert from './../components/Alert.vue'
  import AccountService from '../service/accountService'
  import $ from 'jquery'
  export default{
    data () {
      return {
        cropper: null,
        image: null,
        userImage: null,
        c: 0
      }
    },
    methods: {
      profileImage: function (e) {
        const files = e.target.files || e.dataTransfer.files
        if (!files.length) {
          return
        }
        const reader = new FileReader()
        reader.onload = function (e) {
          $('#profile-image-edit').attr('src', e.target.result)
          $('#profile-image-edit-modal').modal('show')
        }
        reader.readAsDataURL(files[0])
        this.image = files[0]
      },
      save: function () {
        const _this = this
        const data = this.cropper.getData()
        AccountService.postImage(this.image, data.x, data.y, data.width, data.height, function (result) {
          _this.$set(_this, 'c', _this.c + 1)
          _this.$set(_this, 'userImage', _this.$auth.getStatus().getProfileImage() + '?' + _this.c)
        })
        $('#profile-image-edit-modal').modal('hide')
      }
    },
    mounted () {
      const _this = this
      this.$set(this, 'userImage', this.$auth.getStatus().getProfileImage())
      $('#profile-image-edit-modal').on('shown.bs.modal', function (e) {
        _this.cropper = new Cropper($('#profile-image-edit').get(0), {
          responsive: true,
          modal: true,
          aspectRatio: 1
        })
      })
      $('#profile-image-edit-modal').on('hidden.bs.modal', function (e) {
        _this.cropper.destroy()
      })
    },
    watch: {
    },
    created () {
    },
    components: {
      FormGroup,
      Alert
    }
  }
</script>