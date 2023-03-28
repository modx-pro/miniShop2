import MsNotify from './msnotify.class.js'

export default class MsIziToast extends MsNotify {
  show (type, message) {
    if (window[this.config.handlerClassName] && message) {
      const options = Object.assign(this.config.handlerOptions, { title: message })
      try {
        window[this.config.handlerClassName][type](options)
      } catch (e) {
        console.error(e, `Не найден метод ${type} в классе ${this.config.handlerClassName}`)
      }
    }
  }
}
