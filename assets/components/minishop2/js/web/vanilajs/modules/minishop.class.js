export default class MiniShop {
  constructor (miniShop2Config) {
    const defaults = {
      notifyClassPath: './msnotify.class.js',
      notifyClassName: 'MsNotify',
      cartClassPath: './mscart.class.js',
      cartClassName: 'MsCart',
      orderClassPath: './msorder.class.js',
      orderClassName: 'MsOrder',
      moduleImportErrorMsg: 'Произошла ошибка при загрузке модуля',
      properties: ['Message', 'Cart', 'Order'],
      actionUrl: document.location.href,
      formMethod: 'POST',
    }
    this.miniShop2Config = Object.assign(defaults, miniShop2Config)

    this.miniShop2Config.callbacksObjectTemplate = this.callbacksObjectTemplate
    this.Callbacks = this.miniShop2Config.Callbacks = {
      Cart: {
        add: this.miniShop2Config.callbacksObjectTemplate(),
        remove: this.miniShop2Config.callbacksObjectTemplate(),
        change: this.miniShop2Config.callbacksObjectTemplate(),
        clean: this.miniShop2Config.callbacksObjectTemplate(),
      },
      Order: {
        add: this.miniShop2Config.callbacksObjectTemplate(),
        getcost: this.miniShop2Config.callbacksObjectTemplate(),
        clean: this.miniShop2Config.callbacksObjectTemplate(),
        submit: this.miniShop2Config.callbacksObjectTemplate(),
        getrequired: this.miniShop2Config.callbacksObjectTemplate(),
      },
    }
    this.Callbacks.add = this.addCallback.bind(this)
    this.Callbacks.remove = this.removeCallback.bind(this)
    this.actionName = 'ms2_action'
    this.action = '[type="submit"][name=' + this.actionName + ']'
    this.form = '.ms2_form'
    this.formData = null
    this.Message = null
    this.timeout = 300

    this.initialize()
  }

  async setHandler (property) {
    let prefix = property.toLowerCase()
    let response = false
    let messageSettings = false

    if (prefix === 'message') {
      prefix = 'notify'
      response = await this.sendRequest({ url: this.miniShop2Config.notifySettingsPath, method: 'GET' })
      if (response.ok) {
        messageSettings = await response.json()
      }
    }

    const classPath = this.miniShop2Config[prefix + 'ClassPath']
    const className = this.miniShop2Config[prefix + 'ClassName']
    const config = messageSettings ? messageSettings[className] : this

    try {
      const { default: ModuleName } = await import(classPath)
      this[property] = new ModuleName(config)
    } catch (e) {
      throw new Error(this.miniShop2Config.moduleImportErrorMsg)
    }
  }

  async initialize () {
    if (!this.miniShop2Config.properties.length) { throw new Error('Не передан массив имён обработчиков') }

    await this.miniShop2Config.properties.forEach(property => {
      this.setHandler(property)
    })

    document.addEventListener('submit', e => {
      const form = e.target
      const action = form.querySelector(this.action) ? form.querySelector(this.action).value : ''
      if (action) {
        e.preventDefault()
        const formData = new FormData(form)
        const components = this.getObjectMethod(action)

        formData.append(this.actionName, action)
        this.formData = formData
        this[components.object][components.method](this.formData)
      }
    })
  }

  getObjectMethod (action) {
    const actionComponents = action.split('/')
    const object = actionComponents[0].replace(actionComponents[0].substring(0, 1), actionComponents[0].substring(0, 1).toUpperCase())
    const method = actionComponents[1]

    return { object, method }
  }

  callbacksObjectTemplate () {
    return {
      // return false to prevent send data
      before: [],
      response: {
        success: [],
        error: [],
      },
      ajax: {
        done: [],
        fail: [],
        always: [],
      },
    }
  }

  addCallback (path, name, func) {
    if (typeof func !== 'function') {
      return false
    }
    path = path.split('.')
    let obj = this.Callbacks
    for (let i = 0; i < path.length; i++) {
      if (obj[path[i]] === undefined) {
        return false
      }
      obj = obj[path[i]]
    }
    if (typeof obj !== 'object') {
      obj = [obj]
    }
    if (name !== undefined) {
      obj[name] = func
    } else {
      obj.push(func)
    }
    return true
  }

  removeCallback (path, name) {
    path = path.split('.')
    let obj = this.Callbacks
    for (let i = 0; i < path.length; i++) {
      if (obj[path[i]] === undefined) {
        return false
      }
      obj = obj[path[i]]
    }
    if (obj[name] !== undefined) {
      delete obj[name]
      return true
    }
    return false
  }

  runCallback (callback, bind) {
    if (typeof callback === 'function') {
      return callback.apply(bind, Array.prototype.slice.call(arguments, 2))
    } else if (typeof callback === 'object') {
      for (const i in callback) {
        if (Object.prototype.hasOwnProperty.call(callback, i)) {
          const response = callback[i].apply(bind, Array.prototype.slice.call(arguments, 2))
          if (response === false) {
            return false
          }
        }
      }
    }
    return true
  }

  sendRequest (params) {
    const body = params.body || new FormData()
    const headers = params.headers || { 'X-Requested-With': 'XMLHttpRequest' }
    const url = params.url || this.miniShop2Config.actionUrl
    const method = params.method || this.miniShop2Config.formMethod

    let options = { method, headers, body }
    if (method === 'GET') {
      options = { method, headers }
    }

    return fetch(url, options)
  }

  async send (data, callbacks, userCallbacks, headers) {
    // callback before
    if (this.runCallback(callbacks.before) === false || this.runCallback(userCallbacks.before) === false) {
      return
    }

    if (Array.isArray(data)) {
      data.push({
        name: 'ctx',
        value: this.miniShop2Config.ctx,
      })
    } else if (data instanceof FormData) {
      data.append('ctx', this.miniShop2Config.ctx)
    } else if (typeof data === 'string') {
      data += '&ctx=' + this.miniShop2Config.ctx
    }

    const response = await this.sendRequest({ body: data, headers })
    if (response.ok) {
      const result = await response.json()
      if (result.success) {
        this.runCallback(callbacks.response.success, this, result)
        this.runCallback(userCallbacks.response.success, this, result)
        result.message && this.Message.success(result.message)
      } else {
        this.runCallback(callbacks.response.error, this, result)
        this.runCallback(userCallbacks.response.error, this, result)
        result.message && this.Message.error(result.message)
      }
      this.runCallback(callbacks.ajax.done, this, response)
      this.runCallback(userCallbacks.ajax.done, this, response)
    } else {
      this.runCallback(callbacks.ajax.fail, this, response)
      this.runCallback(userCallbacks.ajax.fail, this, response)
    }
    this.runCallback(callbacks.ajax.always, this, response)
    this.runCallback(userCallbacks.ajax.always, this, response)
  }

  formatPrice (price) {
    const pf = this.miniShop2Config.price_format
    price = this.numberFormat(price, pf[0], pf[1], pf[2])

    if (this.miniShop2Config.price_format_no_zeros && pf[0] > 0) {
      price = price.replace(/(0+)$/, '')
      price = price.replace(/[^0-9]$/, '')
    }

    return price
  }

  formatWeight (weight) {
    const wf = this.miniShop2Config.weight_format
    weight = this.numberFormat(weight, wf[0], wf[1], wf[2])

    if (this.miniShop2Config.weight_format_no_zeros && wf[0] > 0) {
      weight = weight.replace(/(0+)$/, '')
      weight = weight.replace(/[^0-9]$/, '')
    }

    return weight
  }

  numberFormat (number, decimals, decPoint, thousandsSep) {
    // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfix by: Michael White (http://crestidg.com)
    let j

    // input sanitation & defaults
    if (isNaN(decimals = Math.abs(decimals))) {
      decimals = 2
    }
    if (typeof decPoint === 'undefined') {
      decPoint = ','
    }
    if (typeof thousandsSep === 'undefined') {
      thousandsSep = '.'
    }

    const i = parseInt(number = (+number || 0).toFixed(decimals)) + ''

    if ((j = i.length) > 3) {
      j = j % 3
    } else {
      j = 0
    }

    const km = j
      ? i.substring(0, j) + thousandsSep
      : ''
    const kw = i.substring(j).replace(/(\d{3})(?=\d)/g, '$1' + thousandsSep)
    const kd = (decimals
      ? decPoint + Math.abs(number - i).toFixed(decimals).replace(/-/, '0').slice(2)
      : '')

    return km + kw + kd
  }
}
