export default class MsNotify {
  constructor (config) {
    this.config = config
    this.initialize()
  }

  initialize () {
    if (this.config.jsPath) {
      const script = document.createElement('script')
      script.src = this.config.jsPath
      script.async = true
      document.body.appendChild(script)
    }

    if (this.config.cssPath) {
      const styles = document.createElement('link')
      styles.href = this.config.cssPath
      styles.rel = 'stylesheet'
      document.head.appendChild(styles)
    }
  }

  show (type, message) {
    if (message !== '') {
      alert(message)
    }
  }

  success (message) {
    this.show('success', message)
  }

  error (message) {
    this.show('error', message)
  }

  close () {
  }
}
