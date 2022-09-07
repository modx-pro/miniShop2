import CustomInputNumber from './custominputnumber.class.js';

export default class msCart {
    constructor(minishop) {
        this.config = minishop.miniShop2Config;
        this.config.inputNumber = this.config.inputNumber || {};
        this.minishop = minishop;
        this.callbacks = {
            add: this.config.callbacksObjectTemplate(),
            remove: this.config.callbacksObjectTemplate(),
            change: this.config.callbacksObjectTemplate(),
            clean: this.config.callbacksObjectTemplate()
        }
        this.cart = document.querySelector('#msCart');
        this.miniCart = document.querySelectorAll('#msMiniCart');
        this.miniCartClass = '.msMiniCart';
        this.miniCartNotEmptyClass = 'full';
        this.countInput = document.querySelectorAll('#msCart input[name=count]');
        this.totalWeight = document.querySelectorAll('.ms2_total_weight');
        this.totalCount = document.querySelectorAll('.ms2_total_count');
        this.totalCost = document.querySelectorAll('.ms2_total_cost');
        this.totalDiscount = document.querySelectorAll('.ms2_total_discount');
        this.cost = '.ms2_cost';
        this.eventSubmit = new Event('submit', {bubbles: true,cancelable: true,});
        this.initialize();
    }

    initialize() {
        if (!this.cart) {
            return;
        }

        this.countInput.forEach((el) => {
            const self = this;
            new CustomInputNumber(el, this.config.inputNumber);
            el.addEventListener('change', () =>{
                if (el.value) {
                    el.closest(self.minishop.form).dispatchEvent(self.eventSubmit);
                }
            });
        });
    }

    add(formData) {
        const callbacks = this.callbacks;
        callbacks.add.response.success = function (response) {
            this.status(response.data);
        }.bind(this);
        this.minishop.send(formData, this.callbacks.add, this.minishop.Callbacks.Cart.add);
    }

    remove(formData) {
        const callbacks = this.callbacks;
        callbacks.remove.response.success = function (response) {
            this.remove_position(formData.get('key'));
            this.status(response.data);
        }.bind(this);
        this.minishop.send(formData, this.callbacks.remove, this.minishop.Callbacks.Cart.remove);
    }

    change(formData) {
        const callbacks = this.callbacks;
        this.formData = this.minishop.formData;
        callbacks.change.response.success = function (response) {
            if (typeof (response.data.key) == 'undefined') {
                this.remove_position(this.formData.get('key'));
            }
            this.status(response.data);
        }.bind(this);
        this.minishop.send(formData, this.callbacks.change, this.minishop.Callbacks.Cart.change);
    }

    status(status) {
        if (status['total_count'] < 1) {
            location.reload();
        } else {
            const $miniCarts_old = Array.prototype.slice.call(this.miniCart),
                $miniCarts_new = Array.prototype.slice.call(document.querySelectorAll(this.miniCartClass)),
                $miniCarts = $miniCarts_old.concat($miniCarts_new);

            if (status['total_count'] > 0 && $miniCarts.length > 0) {
                $miniCarts.forEach((cart) => {
                    if (!cart.classList.contains(this.miniCartNotEmptyClass)) {
                        cart.classList.add(this.miniCartNotEmptyClass);
                    }
                });
            }
            this.totalWeight.forEach(el => el.innerText = this.minishop.formatWeight(status['total_weight']));
            this.totalCount.forEach(el => el.innerText = status['total_count']);
            this.totalCost.forEach(el => el.innerText = this.minishop.formatPrice(status['total_cost']));
            this.totalDiscount.forEach(el => el.innerText = this.minishop.formatPrice(status['total_discount']));

            if (typeof (status['cost']) === 'number') {
                const productCost = document.querySelector('[id="' + status['key'] + '"] ' + this.cost);
                if (productCost) {
                    productCost.innerText = this.minishop.formatPrice(status['cost']);
                }
            }
            if (document.querySelector(this.minishop.Order.orderCost)) {
                this.minishop.Order.getcost();
            }
        }
    }

    clean(formData) {
        const callbacks = this.callbacks;
        callbacks.clean.response.success = function (response) {
            this.status(response.data);
        }.bind(this);

        this.minishop.send(formData, this.callbacks.clean, this.minishop.Callbacks.Cart.clean);
    }

    remove_position(key) {
        document.querySelector('[id="' + key + '"]').remove();
    }
}
