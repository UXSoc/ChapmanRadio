<template>
    <div class="col-md-6 wn-widebox">
        <img src="./../../img/dj-wide.jpeg">
        <div class="wn-widebox-text">
            <p class="wn-wb-title">{{ title }}</p>
            <p class="wn-wb-desc">{{ description }}</p>
        </div>
    </div>
</template>

<script>
  export default{
    data () {
      return {}
    },
    props: {
        title: {
            type: String,
            default: 'Lorem ipsum dolor sit amet consectetur adispiscing elit'
        },
        description: {
            type: String,
            default: 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa'
        }
    },
    methods: {
    }
  }
</script>