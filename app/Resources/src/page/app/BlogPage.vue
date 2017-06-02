<template>
    <div>
        <div class="container">
            <h1 class="cr_header">Blog</h1>
            <div class="row">
                <div class="col-md-8 nopadding">
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                    <post-entry></post-entry>
                </div>
                <div class="col-md-4 nopadding">

                </div>
            </div>
            <div class="row">

            </div>
        </div>
    </div>
</template>

<script>
    import PostEntry from './../../components/PostEntry.vue'

    export default{
      data () {
        return {}
      },
      methods: {

      },
      watch: {
      },
      components: {
        PostEntry
      }
    }
</script>