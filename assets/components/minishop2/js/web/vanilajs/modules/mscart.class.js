import CustomInputNumber from './custominputnumber.class.js'

export default class MsCart {
  constructor (minishop) {
    this.minishop = minishop

    this.callbacks = {
      add: this.minishop.config.callbacksObjectTemplate(),
      remove: this.minishop.config.callbacksObjectTemplate(),
      change: this.minishop.config.callbacksObjectTemplate(),
      clean: this.minishop.config.callbacksObjectTemplate(),
    }

    this.cart = document.querySelector('#msCart')
    this.miniCarts = document.querySelectorAll('#msMiniCart, .msMiniCart')
    this.miniCartNotEmptyClass = 'full'

    this.totalWeight = document.querySelectorAll('.ms2_total_weight')
    this.totalCount = document.querySelectorAll('.ms2_total_count')
    this.totalCost = document.querySelectorAll('.ms2_total_cost')
    this.totalDiscount = document.querySelectorAll('.ms2_total_discount')
    this.cost = '.ms2_cost'

    this.eventSubmit = new Event('submit', { bubbles: true, cancelable: true })

    this.initialize()
  }

  initialize () {
    if (!this.cart) {
      return
    }

    this.cart.querySelectorAll('input[name=count]')?.forEach(el => {
      // eslint-disable-next-line no-new
      new CustomInputNumber(el, this.minishop.config.inputNumber)
      el.addEventListener('change', () => el.value && el.closest(this.minishop.form).dispatchEvent(this.eventSubmit))
    })
  }

  add (formData) {
    this.callbacks.add.response.success = response => this.status(response.data)
    this.minishop.send(formData, this.callbacks.add, this.minishop.Callbacks.Cart.add)
  }

  remove (formData) {
    this.callbacks.remove.response.success = response => {
      this.removePosition(formData.get('key'))
      this.status(response.data)
    }
    this.minishop.send(formData, this.callbacks.remove, this.minishop.Callbacks.Cart.remove)
  }

  change (formData) {
    this.formData = this.minishop.formData
    this.callbacks.change.response.success = response => {
      if (typeof response.data.key === 'undefined') {
        this.removePosition(this.formData.get('key'))
      }
      this.status(response.data)
    }
    this.minishop.send(formData, this.callbacks.change, this.minishop.Callbacks.Cart.change)
  }

  clean (formData) {
    this.callbacks.clean.response.success = response => this.status(response.data)
    this.minishop.send(formData, this.callbacks.clean, this.minishop.Callbacks.Cart.clean)
  }

  status (status) {
    if (status.total_count < 1) {
      location.reload()
    }

    this.miniCarts.forEach(cart => cart.classList.add(this.miniCartNotEmptyClass))

    this.totalWeight.forEach(el => { el.innerText = this.minishop.formatWeight(status.total_weight) })
    this.totalCount.forEach(el => { el.innerText = status.total_count })
    this.totalCost.forEach(el => { el.innerText = this.minishop.formatPrice(status.total_cost) })
    this.totalDiscount.forEach(el => { el.innerText = this.minishop.formatPrice(status.total_discount) })

    if (typeof status.cost === 'number') {
      const productCost = document.querySelector(`[id="${status.key}"] ${this.cost}`)
      if (productCost) {
        productCost.innerText = this.minishop.formatPrice(status.cost)
      }
    }

    if (this.minishop.Order.orderCost) {
      this.minishop.Order.getcost()
    }
  }

  removePosition (key) {
    document.getElementById(key)?.remove()
  }
}
