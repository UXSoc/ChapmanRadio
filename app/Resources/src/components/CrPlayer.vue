<template>
    <div class="bottom-fixed">
        <div class="content-container">
            <div class="color-container"></div>
            <div class="img-container" :style="{ backgroundImage: 'url(' + image + ')' }"></div>
            <div class="blur"></div>
        </div>
        <div class="container-fluid inner nopadding">
            <div class="row marginfix">
                <div class="col-md-4 reddd">

                </div>
                <div class="col-md-4 grennn">

                </div>
                <div class="col-md-4 blueee">

                </div>
            </div>
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