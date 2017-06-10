<template>
    <div class="col-md-3 wnb">
        <router-link class="scb-box" :to="show.getRoute()" :exact="true" tag="a">
            <img src="https://images.genius.com/6d4830a2f394d01e91ef6f378fdb0c76.1000x1000x1.jpg">
            <p class="scb-text scb-text-title"><strong>{{ show.getName()  }}</strong></p>
            <p class="scb-text"><i></i><br><br>{{ show.getExcerpt() }}<br><br></p>
            <!--<p class="scb-genre"><i>{{ genre }}</i></p>-->
            <!--<p class="scb-desc">{{ show_description }}</p>-->
        </router-link>
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