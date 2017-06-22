<template>
    <div>{{route}}/<span v-if="!edit">{{slug}}<a v-on:click.prevent="edit = true"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>
        <input v-else v-model="slug" type="text" v-on:keyup.enter="slugify()"/>
    </div>
</template>

<script>
    export default {
      data () {
        return {
          route: '',
          slug: '',
          edit: false
        }
      },
      props: {
        to: {
          type: Object,
          default: {}
        }
      },
      computed: {
        location: function () {
          return this.$router.resolve(this.to, this.$route, '#').href
        }
      },
      methods: {
        splitify: function () {
          let path = this.$router.resolve(this.to, this.$route, '#').href.split('/')
          this.$set(this, 'slug', path.splice(path.length - 1, 1)[0])
          this.$set(this, 'route', path.join('/'))
        },
        slugify: function () {
          let result = this.slug
          result = result.replace(/(-|\s|\n)+/g, '-')
          this.$set(this, 'edit', false)
          this.$set(this, 'slug', result)
          this.$emit('onSlug', this.slug)
        }
      },
      watch: {
        to: 'splitify'
      },
      created () {
        this.splitify()
      }
    }
</script>