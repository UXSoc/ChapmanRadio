
export let _Vue
export function install (Vue) {
  if (install.installed) return
  install.installed = true

  _Vue = Vue

  Object.defineProperty(Vue.prototype, '$player', {
    get () { return this.$root._player }
  })

  Object.defineProperty(Vue.prototype, '$track', {
    get () { return this.$root._track }
  })

  const isDef = v => v !== undefined
  Vue.mixin({
    beforeCreate () {
      if (isDef(this.$options.player)) {
        this._player = this.$options.player
        this._player.init(this)
        Vue.util.defineReactive(this, '_track', this._player._track)
      }
    },
    destroyed () {
    }
  })
}
