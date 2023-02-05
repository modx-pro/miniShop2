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

        this.total_weight = document.querySelectorAll('[data-ms-cart] [data-ms-cart-weight]');
        this.total_count = document.querySelectorAll('[data-ms-cart] [data-ms-cart-count]');
        this.total_cost = document.querySelectorAll('[data-ms-cart] [data-ms-cart-cost]');
        this.total_discount = document.querySelectorAll('[data-ms-cart] [data-ms-cart-discount]');
        this.full_carts = document.querySelectorAll('[data-ms-cart] [data-ms-cart-full]');
        this.empty_carts = document.querySelectorAll('[data-ms-cart] [data-ms-cart-empty]');
        this.options = document.querySelectorAll('[data-ms-cart] [data-ms-product-options]');
        this.orderForms = document.querySelectorAll('[data-ms-order]');
        this.cost = '[data-ms-product-cost]';

        this.eventSubmit = new Event('submit', {bubbles: true, cancelable: true});

        this.initialize();
    }

    initialize() {
        if (!this.full_carts.length) {
            return;
        }

        this.full_carts.forEach(cart => {
            this.setFieldsHandlers(cart);
        });
    }

    setFieldsHandlers(parent) {
        parent.querySelectorAll('input[name=count], [data-ms-product-options]')?.forEach(el => {
            if (el.name === 'count') {
                new CustomInputNumber(el, this.config.inputNumber);
            }
            el.addEventListener('change', () => el.value && el.closest(this.minishop.form).dispatchEvent(this.eventSubmit));
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
        //console.log(status);

        this.toggleCarts(status.total_count);

        this.orderBlockHandler(status.total_count);

        if (status.html) {
            this.addProductRow(status.html);
        }

        if (status.key_old) {
            this.removePosition(status.key_old);
        }

        if (status.key_new) {
            this.updateProductKey(status.key, status.key_new, status.cart);
        }

        if (status.cart) {
            for (let k in status.cart) {
                this.minishop.setValues(status.cart[k], '[data-ms-product-id="' + status.cart[k]['key'] + '"] [data-ms-product-', ['id', 'key', 'ctx', 'options']);
            }
        }

        this.minishop.setValues(status, '[data-ms-cart-', ['cart', 'key', 'key_new', 'key_old', 'row']);

        if (document.querySelector('[data-ms-order]')) {
            this.minishop.Order.getcost();
        }
    }

    toggleCarts(total_count) {
        if (total_count > 0) {
            this.full_carts.forEach(full => this.minishop.show(full));
            this.empty_carts.forEach(empty => this.minishop.hide(empty));
        } else {
            this.full_carts.forEach(full => {
                this.minishop.hide(full);
                full.querySelectorAll('[data-ms-cart-products]')?.forEach(el => el.innerHTML = '');
            });
            this.empty_carts.forEach(empty => this.minishop.show(empty));
        }
    }

    addProductRow(html) {
        for (let k in html) {
            const cartWraps = document.querySelectorAll(`[data-ms-cart-products="${k}"]`);
            if (cartWraps.length) {
                cartWraps.forEach(cart => {
                    const row = new DOMParser().parseFromString(html[k], "text/html").querySelector('[data-ms-product-id]');
                    this.setFieldsHandlers(row);
                    cart.appendChild(row);
                });
            }
        }
    }

    updateProductKey(key, key_new, cart) {
        const changedProduct = document.querySelectorAll(`[data-ms-product-id="${key}"]`);
        if (changedProduct.length) {
            changedProduct.forEach(product => {
                const keyInputs = product.querySelectorAll('[name="key"]');
                keyInputs?.forEach(el => el.value = key_new);
                product.setAttribute('data-ms-product-id', key_new);

                product.querySelectorAll('[data-ms-product-options]')?.forEach(option => {
                    const optionName = option.name.match(/options\[(.*)\]/)[1];
                    const value = cart[key_new]['options'][optionName];
                    if (option.type === 'checkbox' || option.type === 'radio') {
                        option.checked = cart[key_new]['options'].hasOwnProperty(optionName);
                    }else{
                        option.value = value;
                    }
                });
            });
        }
    }

    removePosition(key) {
        const changedProduct = document.querySelectorAll(`[data-ms-product-id="${key}"]`);
        if (changedProduct.length) {
            changedProduct.forEach(product => product.remove())
        }
    }

    orderBlockHandler(count) {
        if (this.orderForms.length) {
            if (count > 0) {
                this.orderForms.forEach(order => {
                    this.minishop.show(order);
                    order.querySelectorAll('input, select, button, textarea')?.forEach(el => el.disabled = false);
                });
            } else {
                this.orderForms.forEach(order => {
                    this.minishop.hide(order);
                    order.querySelectorAll('input, select, button, textarea')?.forEach(el => el.disabled = true);
                });
            }
        }
    }
}
