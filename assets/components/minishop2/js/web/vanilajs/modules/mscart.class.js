import CustomInputNumber from './custominputnumber.class.js';

export default class MsCart {
    constructor(minishop) {
        this.minishop = minishop;
        this.config = minishop.miniShop2Config;

        this.callbacks = {
            add: this.config.callbacksObjectTemplate(),
            remove: this.config.callbacksObjectTemplate(),
            change: this.config.callbacksObjectTemplate(),
            clean: this.config.callbacksObjectTemplate(),
        }

        this.hiddenClass = this.config.hiddenClass || 'ms-hidden';

        this.total_weight = document.querySelectorAll('[data-ms-cart] [data-ms-cart-weight]');
        this.total_count = document.querySelectorAll('[data-ms-cart] [data-ms-cart-count]');
        this.total_cost = document.querySelectorAll('[data-ms-cart] [data-ms-cart-cost]');
        this.total_discount = document.querySelectorAll('[data-ms-cart] [data-ms-cart-discount]');
        this.full_carts = document.querySelectorAll('[data-ms-cart] [data-ms-cart-full]');
        this.empty_carts = document.querySelectorAll('[data-ms-cart] [data-ms-cart-empty]');
        this.options = document.querySelectorAll('[data-ms-cart] [data-ms-product-options]');
        this.cost = '[data-ms-product-cost]';

        this.eventSubmit = new Event('submit', {bubbles: true, cancelable: true});

        this.initialize();
    }

    initialize() {
        if (!this.full_carts.length) {
            return;
        }

        this.full_carts.forEach(cart => {
            cart.querySelectorAll('input[name=count], [data-ms-product-options]')?.forEach(el => {
                if(el.name === 'count'){
                    new CustomInputNumber(el, this.config.inputNumber);
                }
                el.addEventListener('change', () => el.value && el.closest(this.minishop.form).dispatchEvent(this.eventSubmit));
            });
        });
    }

    add(formData) {
        this.callbacks.add.response.success = response => this.status(response.data);
        this.minishop.send(formData, this.callbacks.add, this.minishop.Callbacks.Cart.add);
    }

    remove(formData) {
        this.callbacks.remove.response.success = response => {
            this.removePosition(formData.get('key'));
            this.status(response.data);
        };
        this.minishop.send(formData, this.callbacks.remove, this.minishop.Callbacks.Cart.remove);
    }

    change(formData) {
        this.formData = this.minishop.formData;
        this.callbacks.change.response.success = response => {
            if (typeof response.data.key === 'undefined') {
                this.removePosition(this.formData.get('key'));
            }
            this.status(response.data);
        };
        this.minishop.send(formData, this.callbacks.change, this.minishop.Callbacks.Cart.change);
    }

    clean(formData) {
        this.callbacks.clean.response.success = response => this.status(response.data);
        this.minishop.send(formData, this.callbacks.clean, this.minishop.Callbacks.Cart.clean);
    }

    status(status) {
        if (status.total_count < 1) {
            this.full_carts.forEach(full => {
                full.classList.add('ms-hidden');
                full.querySelectorAll('[data-ms-cart-products]')?.forEach(el => el.innerHTML = '');
            });
            this.empty_carts.forEach(empty => empty.classList.remove(this.hiddenClass));
        } else {
            this.full_carts.forEach(full => full.classList.remove(this.hiddenClass));
            this.empty_carts.forEach(empty => empty.classList.add(this.hiddenClass));
        }
        console.log(status);

        if (status.html) {
            for (let k in status.html) {
                const cartWraps = document.querySelectorAll(`[data-ms-cart-products="${k}"]`);
                if (cartWraps.length) {
                    cartWraps.forEach(cart => {
                        cart.innerHTML += status.html[k];
                    });
                }
            }
            this.initialize();
        }

        if(status.key_old){
            this.removePosition(status.key_old);
        }
        this.setTotals(status);
        
        const changedProduct = document.querySelectorAll(`[data-ms-product-id="${status.key}"]`);
        if (changedProduct.length) {
            changedProduct.forEach(product => {
                if (typeof status.cost === 'number') {
                    const productCost = product.querySelector(`${this.cost}`);
                    if (productCost) {
                        productCost.innerText = this.minishop.formatPrice(status.cost)
                    }
                }
                if (status.row) {
                    const productCount = product.querySelector(`[name="count"]`);
                    if (productCount) {
                        productCount.value = status.row.count
                    }
                }
                if(status.key_new){
                    const keyInputs = product.querySelectorAll('[name="key"]');
                    const optionInputs = product.querySelectorAll('[name*="options"]');
                    optionInputs?.forEach(el => {
                        const optionName = el.name.match(/options\[(.*?)\]/);
                        el.value = status.row.options[optionName[1]];
                        if(el.type === 'checkbox' || el.type === 'radio'){
                            el.checked = true;
                        }
                    });
                    keyInputs?.forEach(el => el.value = status.key_new);
                    product.setAttribute('data-ms-product-id', status.key_new);

                }
            });
        }

        if (this.minishop.Order.orderCost) {
            this.minishop.Order.getcost();
        }
    }

    setTotals(status) {
        const keys = ['total_weight', 'total_count', 'total_cost', 'total_discount'];
        keys.forEach(key => {
            if (status[key]) {
                this[key].forEach(el => {
                    el.innerText = this.minishop.formatWeight(status[key]);
                });
            }
        });
    }

    removePosition(key) {
        const changedProduct = document.querySelectorAll(`[data-ms-product-id="${key}"]`);
        if (changedProduct.length) {
            changedProduct.forEach(product => product.remove())
        }
    }
}
