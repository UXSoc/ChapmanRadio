<template>
    <div class="open tag-collection">

        <div v-on:click.prevent="tagInputEdit">
            <span v-for="(tag,index) in tags" v-on:click="$event.stopPropagation()" class="tag label label-info">
                {{tag}}
                <a v-on:click.prevent="removeTag(index)">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
            </span>
            <input type="text" ref="tagInput" v-model="entry" v-on:keyup.enter="addTag(entry)"/>
        </div>
        <!--<ul class="dropdown-menu">-->
            <!--<li><a href="#">Action</a></li>-->
            <!--<li><a href="#">Another action</a></li>-->
            <!--<li><a href="#">Something else here</a></li>-->
            <!--<li role="separator" class="divider"></li>-->
            <!--<li><a href="#">Separated link</a></li>-->
        <!--</ul>-->
    </div>
</template>

<script>
  import $ from 'jquery'
  export default{
    props: {
      tags: {
        type: Array,
        default: () => []
      }
    },
    data () {
      return {
        entry: ''
      }
    },
    methods: {
      removeTag (index) {
        this.tags.splice(index, 1)
      },
      addTag (tag: string) {
        this.tags.push(this.entry)
        this.entry = ''
      },
      tagInputEdit () {
        $(this.$refs.tagInput).focus()
      },
      clickTag (e) {
        e.stopPropagation()
      }
    }
  }
</script>