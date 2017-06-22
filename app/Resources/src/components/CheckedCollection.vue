<template>
    <div>
        <template v-for="item in collection">
            <div>
                <input type="checkbox" v-on:change="toggled(item)" :checked="isSelected(selectedItems,item)"/>
                <slot name="item" :item="item"></slot>
            </div>
        </template>
    </div>
</template>

<script>
  export default{
    props: {
      collection: {
        type: Array,
        default: () => []
      },
      selectedItems: {
        type: Array,
        default: () => []
      },
      isSelected: {
        type: Function,
        default: () => false
      }
    },
    methods: {
      toggled: function (item) {
        if (this.isSelected(this.selectedItems, item)) {
          this.$emit('onItemRemoved', item)
        } else {
          this.$emit('onItemAdded', item)
        }
      }
    },
    data () {
      return {
      }
    }
  }
</script>