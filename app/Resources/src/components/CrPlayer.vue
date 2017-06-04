<template>
    <div class="content-container">
        <div style="background-color:#313D31;;height:100px;width:100%;z-index:1001"></div>
        <div class="img-container" :style="{ backgroundImage: 'url(' + image + ')' }"></div>
        <div class="blur"></div>
        <div>
            <img :src="image">
            <p>Planet Moon<br>Passionfruit<br>Drake</p>
            <a class="btn btn-full" href="#">VIEW SHOW PAGE</a>
            <span>
                play button goes here
            </span>
            <span>
                volume control goes here
            </span>
            <i class="fa fa-chevron-up" aria-hidden="true"></i>
        </div>
    </div>
</template>

<script>
    export default{
        data () {
            return {}
        },
        props: {
            image: {
                type: String,
                default: ''
            }
        },
        methods: {
        }
    }
</script>