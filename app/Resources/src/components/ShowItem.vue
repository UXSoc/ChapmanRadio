<template>
    <div class="post-entry" v-if="show">
        <h2><router-link :to="show.getRoute()" :exact="true" tag="a">{{show.getName()}}</router-link></h2>
        <div v-html="show.getDescription()"></div>
        <slot></slot>
    </div>
</template>

<script>
    import Show from '../entity/show'
    export default{
      props: {
        show: {
          type: Show,
          default: new Show({})
        }
      },
      data () {
        return {}
      }
    }
</script>