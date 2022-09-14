export default class msOrder {
    constructor(minishop) {
        this.config = minishop.miniShop2Config;
        this.minishop = minishop;
        this.callbacks = {
            add: this.config.callbacksObjectTemplate(),
            getcost: this.config.callbacksObjectTemplate(),
            clean: this.config.callbacksObjectTemplate(),
            submit: this.config.callbacksObjectTemplate(),
            getrequired: this.config.callbacksObjectTemplate()
        };
        this.order = document.querySelector('#msOrder');
        this.deliveryInput = 'input[name="delivery"]';
        this.inputParent = '.input-parent';
        this.paymentInput = 'input[name="payment"]';
        this.paymentInputUniquePrefix = '#payment_';
        this.deliveryInputUniquePrefix = '#delivery_';
        this.orderCost = document.querySelector('#ms2_order_cost');
        this.cartCost = document.querySelector('#ms2_order_cart_cost');
        this.deliveryCost = document.querySelector('#ms2_order_delivery_cost');
        this.changeEvent = new Event('change', {bubbles: true, cancelable: true,});
        this.clickEvent = new Event('click', {bubbles: true, cancelable: true,});
        this.initialize();
    }

    initialize() {
        if (this.order) {
            const cleanBtn = this.order.querySelector(`[name="${this.minishop.actionName}"][value="order/clean"]`),
                inputs = this.order.querySelectorAll('input, textarea'),
                self = this;
            if (cleanBtn) {
                cleanBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    self.clean();
                });
            }
            if (inputs) {
                inputs.forEach(el => {
                    el.addEventListener('change', (e) => {
                        e.preventDefault();
                        if (el.value) {
                            self.add(el.name, el.value);
                        }
                    });
                });
            }

            const $deliveryInputChecked = this.order.querySelector(this.deliveryInput + ':checked');
            if ($deliveryInputChecked) {
                $deliveryInputChecked.dispatchEvent(this.changeEvent);
            }
        }
    }

    hide(node) {
        node.classList.add('ms-hidden');
        node.checked = false;
    }

    show(node) {
        node.classList.remove('ms-hidden');
    }

    updatePayments(payments) {
        payments = payments.replace(/[\[\]]/g, '').split(',');
        let $paymentInputs = this.order.querySelectorAll(this.paymentInput);
        if ($paymentInputs) {
            $paymentInputs = Array.prototype.slice.call($paymentInputs);
            $paymentInputs.forEach(el => {
                el.disabled = true;
                this.hide(el.closest(this.inputParent));
            });

            if (payments.length) {
                for (let i in payments) {
                    let selector = this.paymentInputUniquePrefix + payments[i],
                        input = $paymentInputs.find(item => '#' + item.id === selector);
                    if (input) {
                        input.disabled = false;
                        this.show(input.closest(this.inputParent));
                    }
                }
            }
            const checked = $paymentInputs.filter(el => el.checked && (el.offsetWidth > 0 || el.offsetHeight > 0)),
                visible = $paymentInputs.filter(el => (el.offsetWidth > 0 || el.offsetHeight > 0));
            if (!checked.length) {
                visible[0].checked = true;
            }
        }
    }

    add(key, value) {
        const callbacks = this.callbacks,
            old_value = value;

        callbacks.add.response.success = (response) => {
            let field = this.order.querySelector(`[name="${key}"]`);
            if(response.data.delivery){
                field = document.querySelector(this.deliveryInputUniquePrefix + response.data[key]);
                if (response.data[key] !== old_value) {
                    field.dispatchEvent(this.clickEvent);
                } else {
                    this.getrequired(value);
                    this.updatePayments(field.dataset.payments);
                    this.getcost();
                }
            }
            if(response.data.payment){
                field = document.querySelector(this.paymentInputUniquePrefix + response.data[key]);
                if (response.data[key] !== old_value) {
                    field.dispatchEvent(this.clickEvent);
                } else {
                    this.getcost();
                }
            }
            field.value = response.data[key];
            field.classList.remove('error');
            field.closest(this.inputParent).classList.remove('error');
        }

        callbacks.add.response.error = () => {
            let field = this.order.querySelector(`[name="${key}"]`);
            if (field.getAttribute('type') === 'checkbox' || field.getAttribute('type') === 'radio') {
                field.closest(this.inputParent).classList.add('error');
            } else {
                field.classList.add('error');
            }
        };

        const formData = new FormData();
        formData.append('key', key);
        formData.append('value', value);
        formData.append(this.minishop.actionName, 'order/add');
        this.minishop.send(formData, this.callbacks.add, this.minishop.Callbacks.Order.add);
    }

    getcost() {
        const callbacks = this.callbacks;
        callbacks.getcost.response.success = (response) => {
            if(this.orderCost){
                this.orderCost.innerText = this.minishop.formatPrice(response.data['cost']);
            }
            if(this.cartCost){
                this.cartCost.innerText = this.minishop.formatPrice(response.data['cart_cost']);
            }
            if(this.deliveryCost){
                this.deliveryCost.innerText = this.minishop.formatPrice(response.data['delivery_cost']);
            }
        };

        const formData = new FormData();
        formData.append(this.minishop.actionName, 'order/getcost');
        this.minishop.send(formData, this.callbacks.getcost, this.minishop.Callbacks.Order.getcost);
    }

    clean() {
        const callbacks = this.callbacks;
        callbacks.clean.response.success = () => location.reload();

        const formData = new FormData();
        formData.append(this.minishop.actionName, 'order/clean');
        this.minishop.send(formData, this.callbacks.clean, this.minishop.Callbacks.Order.clean);
    }

    submit(formData) {
        this.minishop.Message.close();
        const callbacks = this.callbacks;
        callbacks.submit.before = () => {
            const elements = this.order.querySelectorAll('button, a');
            elements.forEach(el => el.disabled = false);
        };
        callbacks.submit.response.success = (response) => {
            if (response.data['redirect']) {
                document.location.href = response.data['redirect'];
            } else if (response.data['msorder']) {
                document.location.href = document.location.origin + document.location.pathname
                    + (document.location.search ? document.location.search + '&' : '?')
                    + 'msorder=' + response.data['msorder'];
            } else {
                location.reload();
            }
        };
        callbacks.submit.response.error = (response) => {
            setTimeout(() => {
                const elements = this.order.querySelectorAll('button, a');
                elements.forEach(el => el.disabled = false);
            }, 3 * this.minishop.timeout);

            const fields = this.order.querySelectorAll( '[name]');
            if (fields) {
                fields.forEach(el => {
                    el.classList.remove('error');
                    if (el.closest(this.inputParent)) {
                        el.closest(this.inputParent).classList.remove('error');
                    }
                });
            }

            for (let i in response.data) {
                if (response.data.hasOwnProperty(i)) {
                    const key = response.data[i],
                        field = this.order.querySelector(`[name="${key}"]`);
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        field.closest(this.inputParent).classList.add('error');
                    } else {
                        field.classList.add('error');
                    }
                }
            }
        };
        return this.minishop.send(formData, this.callbacks.submit, this.minishop.Callbacks.Order.submit);
    }

    getrequired(value) {
        const callbacks = this.callbacks;
        callbacks.getrequired.response.success = (response) => {
            const fields = this.order.querySelectorAll( '[name]'),
                requires = response.data['requires'];
            if (fields) {
                fields.forEach(el => {
                    el.classList.remove('required');
                    if (el.closest(this.inputParent)) {
                        el.closest(this.inputParent).classList.remove('required');
                    }
                });
            }

            for (let i = 0, length = requires.length; i < length; i++) {
                let field = this.order.querySelector(`[name="${requires[i]}"]`);
                if (field) {
                    field.classList.add('required');
                    if (field.closest(this.inputParent)) {
                        field.closest(this.inputParent).classList.add('required');
                    }
                }
            }
        };
        callbacks.getrequired.response.error = () => {
            const fields = this.order.querySelectorAll('[name]');
            if (fields) {
                fields.forEach(el => {
                    el.classList.remove('required');
                    if (el.closest(this.inputParent)) {
                        el.closest(this.inputParent).classList.remove('required');
                    }
                });
            }
        };

        const formData = new FormData();
        formData.append('id', value);
        formData.append(this.minishop.actionName, 'order/getrequired');
        this.minishop.send(formData, this.callbacks.getrequired, this.minishop.Callbacks.Order.getrequired);
    }
}
