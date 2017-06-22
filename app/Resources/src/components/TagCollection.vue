<template>
    <div class="open tag-collection">
        <div v-on:click.prevent="tagInputEdit">
            <span v-on:click.prevent="clickTag" v-for="tag in collection" v-on:click="$event.stopPropagation()">
                <slot name="tag" :tag="tag" :removeTag="removeTag"></slot>
            </span>
            <input type="text" ref="tagInput" v-model="tagEntry" v-on:keyup.enter="addTag()"/>
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
  import Tag from '../entity/tag'
  import $ from 'jquery'
  export default{
    props: {
      collection: {
        type: Array,
        default: () => []
      }
    },
    data () {
      return {
        tagEntry: ''
      }
    },
    methods: {
      removeTag (tag: Tag) {
        this.$emit('onItemRemoved', tag)
      },
      addTag () {
        if (this.tagEntry !== '') {
          this.$emit('onItemAdded', Tag.createTag(this.tagEntry))
        }
        this.tagEntry = ''
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