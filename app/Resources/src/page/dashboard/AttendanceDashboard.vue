<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">Attendance</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Recording Utility
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                Record for:
                                <input type="radio" name="grouptype" value="everyone"> Everyone<br>
                                <input type="radio" name="grouptype" value="class"> Class<br>
                                <input type="radio" name="grouptype" value="newdjsandclass"> New DJs & Class<br>
                            </div>
                            <div class="col-md-4">
                                Workshop Date:
                                Recording Method:
                                <!--<datepicker>-->
                                <input type="radio" name="recording1" value="manual"> Manual Recording<br>
                                <input type="radio" name="recording2" value="cardswiper"> CardSwiper<br>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" placeholder="Type student ID, name, or DJ number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary btn-lg btn-block">MARK PRESENT</button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary btn-lg btn-block">MARK EXCUSED</button>

                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary btn-lg btn-block">MARK ABSENT</button>
                            </div>
                            <div class="col-md-4">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <attendance-item></attendance-item>
                            </div>
                            <div class="col-md-4">
                                <attendance-item></attendance-item>
                            </div>
                            <div class="col-md-4">
                                <attendance-item></attendance-item>
                            </div>
                            <div class="col-md-4">
                                <attendance-item></attendance-item>
                            </div>
                            <div class="col-md-4">
                                <attendance-item></attendance-item>
                            </div>
                            <div class="col-md-4">
                                <attendance-item></attendance-item>
                            </div>
                            <div class="col-md-4">
                                <attendance-item></attendance-item>
                            </div>
                            <div class="col-md-4">
                                <attendance-item></attendance-item>
                            </div>
                            <div class="col-md-4">
                                <attendance-item></attendance-item>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    /* @flow */
    import AttendanceItem from '../../components/AttendanceItem.vue'
    export default{
        data () {
            return {
            }
        },
        methods: {

        },
        watch: {
        },
        components: {
            AttendanceItem
        }
    }
</script>