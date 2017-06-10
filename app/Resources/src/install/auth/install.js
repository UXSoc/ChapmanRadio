
export let _Vue
export function install (Vue) {
  if (install.installed) return
  install.installed = true

  _Vue = Vue

  Object.defineProperty(Vue.prototype, '$auth', {
    get () { return this.$root._auth }
  })

  const isDef = v => v !== undefined
  Vue.mixin({
    beforeCreate () {
      if (isDef(this.$options.player)) {
        this._auth = this.$options.auth
        this._auth.init(this)
        Vue.util.defineReactive(this, '_auth', this._auth)
      }
    },
    destroyed () {
    }
  })
}
