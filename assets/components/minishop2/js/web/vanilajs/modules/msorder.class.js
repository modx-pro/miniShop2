export default class MsOrder {
  constructor (minishop) {
    this.minishop = minishop

    this.callbacks = {
      add: this.minishop.config.callbacksObjectTemplate(),
      getcost: this.minishop.config.callbacksObjectTemplate(),
      clean: this.minishop.config.callbacksObjectTemplate(),
      submit: this.minishop.config.callbacksObjectTemplate(),
      getrequired: this.minishop.config.callbacksObjectTemplate(),
    }

    this.order = document.querySelector('#msOrder')
    this.deliveryInput = 'input[name="delivery"]'
    this.inputParent = '.input-parent'
    this.paymentInput = 'input[name="payment"]'
    this.paymentInputUniquePrefix = '#payment_'
    this.deliveryInputUniquePrefix = '#delivery_'

    this.orderCost = document.querySelector('#ms2_order_cost')
    this.cartCost = document.querySelector('#ms2_order_cart_cost')
    this.deliveryCost = document.querySelector('#ms2_order_delivery_cost')

    this.changeEvent = new Event('change', { bubbles: true, cancelable: true })
    this.clickEvent = new Event('click', { bubbles: true, cancelable: true })

    this.initialize()
  }

  initialize () {
    if (this.order) {
      const cleanBtn = this.order.querySelector(`[name="${this.minishop.actionName}"][value="order/clean"]`)
      const inputs = this.order.querySelectorAll('input, textarea')

      if (cleanBtn) {
        cleanBtn.addEventListener('click', e => {
          e.preventDefault()
          this.clean()
        })
      }

      if (inputs) {
        inputs.forEach(el => {
          el.addEventListener('change', e => {
            e.preventDefault()
            el.value && this.add(el.name, el.value)
          })
        })
      }

      const deliveryInputChecked = this.order.querySelector(this.deliveryInput + ':checked')
      if (deliveryInputChecked) {
        deliveryInputChecked.dispatchEvent(this.changeEvent)
      }
    }
  }

  updatePayments (payments) {
    payments = payments.replace(/[[\]]/g, '').split(',')
    let paymentInputs = this.order.querySelectorAll(this.paymentInput)
    if (paymentInputs) {
      paymentInputs = Array.from(paymentInputs)
      paymentInputs.forEach(el => {
        el.disabled = true
        MsOrder.hide(el.closest(this.inputParent))
      })

      if (payments.length) {
        for (const i in payments) {
          const selector = this.paymentInputUniquePrefix + payments[i]
          const input = paymentInputs.find(item => '#' + item.id === selector)

          if (input) {
            input.disabled = false
            MsOrder.show(input.closest(this.inputParent))
          }
        }
      }

      const checked = paymentInputs.filter(el => el.checked && (el.offsetWidth > 0 || el.offsetHeight > 0))
      const visible = paymentInputs.filter(el => (el.offsetWidth > 0 || el.offsetHeight > 0))
      if (!checked.length) {
        visible[0].checked = true
      }
    }
  }

  add (key, value) {
    const oldValue = value

    this.callbacks.add.response.success = response => {
      let field = this.order.querySelector(`[name="${key}"]`)

      if (response.data.delivery) {
        field = document.querySelector(this.deliveryInputUniquePrefix + response.data[key])
        if (response.data[key] !== oldValue) {
          field.dispatchEvent(this.clickEvent)
        } else {
          this.getrequired(value)
          this.updatePayments(field.dataset.payments)
          this.getcost()
        }
      }

      if (response.data.payment) {
        field = document.querySelector(this.paymentInputUniquePrefix + response.data[key])
        if (response.data[key] !== oldValue) {
          field.dispatchEvent(this.clickEvent)
        } else {
          this.getcost()
        }
      }

      field.value = response.data[key] || ''
      field.classList.remove('error')
      field.closest(this.inputParent).classList.remove('error')
    }

    this.callbacks.add.response.error = () => {
      const field = this.order.querySelector(`[name="${key}"]`)
      if (['checkbox', 'radio'].includes(field.type)) {
        field.closest(this.inputParent).classList.add('error')
      } else {
        field.classList.add('error')
      }
    }

    const formData = new FormData()
    formData.append('key', key)
    formData.append('value', value)
    formData.append(this.minishop.actionName, 'order/add')
    this.minishop.send(formData, this.callbacks.add, this.minishop.Callbacks.Order.add)
  }

  getcost () {
    this.callbacks.getcost.response.success = response => {
      if (this.orderCost) {
        this.orderCost.innerText = this.minishop.formatPrice(response.data.cost)
      }

      if (this.cartCost) {
        this.cartCost.innerText = this.minishop.formatPrice(response.data.cart_cost)
      }

      if (this.deliveryCost) {
        this.deliveryCost.innerText = this.minishop.formatPrice(response.data.delivery_cost)
      }
    }

    const formData = new FormData()
    formData.append(this.minishop.actionName, 'order/getcost')
    this.minishop.send(formData, this.callbacks.getcost, this.minishop.Callbacks.Order.getcost)
  }

  clean () {
    this.callbacks.clean.response.success = () => location.reload()

    const formData = new FormData()
    formData.append(this.minishop.actionName, 'order/clean')
    this.minishop.send(formData, this.callbacks.clean, this.minishop.Callbacks.Order.clean)
  }

  submit (formData) {
    this.minishop.Message.close()

    this.callbacks.submit.before = () => {
      const elements = this.order.querySelectorAll('button, a')
      elements.forEach(el => {
        el.disabled = true
      })
    }

    this.callbacks.submit.response.success = response => {
      switch (true) {
        case Boolean(response.data.redirect) :
          document.location.href = response.data.redirect
          break
        case Boolean(response.data.msorder):
          document.location.href = document.location.origin + document.location.pathname +
                    (document.location.search ? document.location.search + '&' : '?') +
                    'msorder=' + response.data.msorder
          break
        default:
          location.reload()
      }
    }

    this.callbacks.submit.response.error = response => {
      setTimeout(() => {
        const elements = this.order.querySelectorAll('button, a')
        elements.forEach(el => {
          el.disabled = false
        })
      }, 3 * this.minishop.timeout)

      if (this.order.elements) {
        Array.from(this.order.elements).forEach(el => {
          el.classList.remove('error')
          el.closest(this.inputParent)?.classList.remove('error')
        })
      }

      for (const i in response.data) {
        if (Object.prototype.hasOwnProperty.call(response.data, i)) {
          const key = response.data[i]
          const field = this.order.querySelector(`[name="${key}"]`)

          if (['checkbox', 'radio'].includes(field.type)) {
            field.closest(this.inputParent).classList.add('error')
          } else {
            field.classList.add('error')
          }
        }
      }
    }

    return this.minishop.send(formData, this.callbacks.submit, this.minishop.Callbacks.Order.submit)
  }

  getrequired (value) {
    this.callbacks.getrequired.response.success = response => {
      const { requires } = response.data

      if (this.order.elements.length) {
        Array.from(this.order.elements).forEach(el => {
          el.classList.remove('required')
          el.closest(this.inputParent)?.classList.remove('required')
        })
      }

      for (const name of requires) {
        this.order.elements[name]?.classList.add('required')
        this.order.elements[name]?.closest(this.inputParent)?.classList.add('required')
      }
    }

    this.callbacks.getrequired.response.error = () => {
      if (this.order.elements.length) {
        Array.from(this.order.elements).forEach(el => {
          el.classList.remove('required')
          el.closest(this.inputParent)?.classList.remove('required')
        })
      }
    }

    const formData = new FormData()
    formData.append('id', value)
    formData.append(this.minishop.actionName, 'order/getrequired')
    this.minishop.send(formData, this.callbacks.getrequired, this.minishop.Callbacks.Order.getrequired)
  }

  static hide (node) {
    node.classList.add('ms-hidden')
    node.checked = false
  }

  static show (node) {
    node.classList.remove('ms-hidden')
  }
}
