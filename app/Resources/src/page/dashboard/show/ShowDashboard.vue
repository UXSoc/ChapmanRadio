<template>
    <div class="col-lg-12">
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#shows" aria-controls="shows" role="tab" data-toggle="tab">Show</a></li>
                <li role="presentation"><a href="#dj" aria-controls="dj" role="tab" data-toggle="tab">Dj's</a></li>
            </ul>
            <div class="tab-content">

                <div role="tabpanel" class="tab-pane active" id="shows">
                    <br>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-8">

                                <!--{{ form_widget(show_form.description, { 'attr': {'class': 'markdown-editor'} }) }}-->
                                <!--{<textarea class="markdown-editor"></textarea>}-->
                            </div>
                            <div class="col-lg-4">
                                <div  class="panel panel-default">
                                    <div class="panel-heading">
                                        Publish
                                    </div>
                                    <div class="panel-body">
                                    </div>
                                </div>

                                <div  class="panel panel-default">
                                    <div class="panel-heading">
                                        Categories
                                    </div>
                                    <div class="panel-body">
                                    </div>
                                </div>

                                <div  class="panel panel-default">
                                    <div class="panel-heading">
                                        Tags
                                    </div>
                                    <div class="panel-body">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="dj">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default{
    data () {
//      this.$route.params.id
      return {}
    },
    methods: {

    },
    watch: {
    },
    components: {

    }
  }
</script>