<template>
    <div class="post-entry">
        <h2><router-link :to="{name: 'post_single', params: { token:getToken(), slug:getSlug()}}" :exact="true" tag="a">{{getName()}}</router-link></h2>
        <div>
            {{getContent()}}
        </div>
    </div>
</template>

<script>
    import Post from '../mixins/postmixin'
    export default{
      mixins: [Post],
      data () {
        return {}
      }
    }
</script>