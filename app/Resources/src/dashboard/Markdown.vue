<template>

    <div class="dashboard-editor">
        <div class="dashboard-tool-bar">
            <div class="float-left">
            </div>
            <div class="float-right">
                <div class="small">
                    <a class="button">Visual</a>
                    <a class="button">Text</a>
                </div>
            </div>
        </div>
        <div class="dashboard-editor-head">
            <div class="float-left">
                <i class="fa fa-bold" aria-hidden="true"></i>
            </div>
        </div>
        <div class="dashboard-editor-content">
            <div class="row expanded">
                <div class="large-6 columns"><textarea :value="input" @input="update"></textarea></div>
                <div class="large-6 columns"><vue-markdown :source="compiledMarkdown"></vue-markdown></div>
            </div>


        </div>
    </div>

</template>

<script>
  import VueMarkdown from 'vue-markdown'

  export default{
    data() {
      return {
        input: '# hello'
      }
    },
    computed: {
      compiledMarkdown: function () {
        return this.input
      }
    },
    created() {
    },
    methods: {
      update: debounce(300,function (e) {
        this.input = e.target.value

        var el = $("textarea")
        el.height("auto")
        el.height(el[0].scrollHeight)

      })
    },
    components: {
      VueMarkdown
    }
  }
</script>

<style>

</style>