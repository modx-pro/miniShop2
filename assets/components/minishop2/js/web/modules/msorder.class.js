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
        this.order = '#msOrder';
        this.deliveries = '#deliveries';
        this.payments = '#payments';
        this.deliveryInput = 'input[name="delivery"]';
        this.inputParent = '.input-parent';
        this.paymentInput = 'input[name="payment"]';
        this.paymentInputUniquePrefix = '#payment_';
        this.deliveryInputUniquePrefix = '#delivery_';
        this.orderCost = '#ms2_order_cost';
        this.cartCost = '#ms2_order_cart_cost';
        this.deliveryCost = '#ms2_order_delivery_cost';
        this.changeEvent = new Event('change', {bubbles: true, cancelable: true,});
        this.clickEvent = new Event('click', {bubbles: true, cancelable: true,});
        this.initialize();
    }

    initialize() {
        if (document.querySelector(this.order)) {
            const cleanBtn = document.querySelector(this.order + ' [name="' + this.minishop.actionName + '"][value="order/clean"]'),
                inputs = document.querySelectorAll(this.order + ' input'),
                textareas = document.querySelectorAll(this.order + ' textarea'),
                self = this;
            if (cleanBtn) {
                cleanBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    self.clean();
                });
            }
            if (inputs && textareas) {
                const inputs_arr = Array.prototype.slice.call(inputs) || [],
                    textareas_arr = Array.prototype.slice.call(textareas) || [],
                    fields = inputs_arr.concat(textareas_arr);
                if (fields) {
                    fields.forEach(el => {
                        el.addEventListener('change', (e) => {
                            e.preventDefault();
                            if (el.value) {
                                self.add(el.name, el.value);
                            }
                        });
                    });
                }
            }

            const $deliveryInputChecked = document.querySelector(this.order + ' ' + this.deliveryInput + ':checked');
            if ($deliveryInputChecked) {
                $deliveryInputChecked.dispatchEvent(this.changeEvent);
            }
        }
    }

    hide(node) {
        node.style.display = 'none';
    }

    show(node) {
        node.style.display = 'block';
    }

    updatePayments(payments) {
        payments = payments.replace(/[\[\]]/g, '').split(',');
        let $paymentInputs = document.querySelectorAll(this.order + ' ' + this.paymentInput);
        if ($paymentInputs) {
            $paymentInputs = Array.prototype.slice.call($paymentInputs);
            $paymentInputs.forEach(el => {
                el.disabled = true;
                this.hide(el.closest(this.inputParent));
            });

            if (payments.length > 0) {
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

        callbacks.add.response.success = function (response) {
            (function (key, value, old_value) {
                let $field = document.querySelector(this.order + ' [name="' + key + '"]');
                switch (key) {
                    case 'delivery':
                        $field = document.querySelector(this.deliveryInputUniquePrefix + response.data[key]);
                        if (response.data[key] != old_value) {
                            $field.dispatchEvent(this.clickEvent);
                        } else {
                            this.getrequired(value);
                            this.updatePayments($field.dataset.payments);
                            this.getcost();
                        }
                        break;
                    case 'payment':
                        $field = document.querySelector(this.paymentInputUniquePrefix + response.data[key]);
                        if (response.data[key] != old_value) {
                            $field.dispatchEvent(this.clickEvent);
                        } else {
                            this.getcost();
                        }
                        break;
                }
                $field.value = response.data[key];
                $field.classList.remove('error');
                $field.closest(this.inputParent).classList.remove('error');

            }.bind(this))(key, value, old_value);
        }.bind(this);

        callbacks.add.response.error = function () {
            (function (key) {
                let $field = document.querySelector(this.order + ' [name="' + key + '"]');
                if ($field.getAttribute('type') === 'checkbox' || $field.getAttribute('type') === 'radio') {
                    $field.closest(this.inputParent).classList.add('error');
                } else {
                    $field.classList.add('error');
                }
            }.bind(this))(key);
        }.bind(this);

        const formData = new FormData();
        formData.append('key', key);
        formData.append('value', value);
        formData.append(this.minishop.actionName, 'order/add');
        this.minishop.send(formData, this.callbacks.add, this.minishop.Callbacks.Order.add);
    }

    getcost() {
        const callbacks = this.callbacks;
        callbacks.getcost.response.success = function (response) {
            const orderCost =  document.querySelector(this.orderCost),
                cartCost =  document.querySelector(this.cartCost),
                deliveryCost =  document.querySelector(this.deliveryCost);
            if(orderCost){
                orderCost.innerText = this.minishop.formatPrice(response.data['cost']);
            }
            if(cartCost){
                cartCost.innerText = this.minishop.formatPrice(response.data['cart_cost']);
            }
            if(deliveryCost){
                deliveryCost.innerText = this.minishop.formatPrice(response.data['delivery_cost']);
            }
        }.bind(this);
        const formData = new FormData();
        formData.append(this.minishop.actionName, 'order/getcost');
        this.minishop.send(formData, this.callbacks.getcost, this.minishop.Callbacks.Order.getcost);
    }

    clean() {
        const callbacks = this.callbacks;
        callbacks.clean.response.success = function () {
            location.reload();
        };

        const formData = new FormData();
        formData.append(this.minishop.actionName, 'order/clean');
        this.minishop.send(formData, this.callbacks.clean, this.minishop.Callbacks.Order.clean);
    }

    submit(formData) {
        this.minishop.Message.close();
        const callbacks = this.callbacks;
        callbacks.submit.before = function () {
            const elements = this.minishop.querySelectorsArray([this.order + ' button', this.order + ' a']);
            elements.forEach(el => el.disabled = false);
        }.bind(this);
        callbacks.submit.response.success = function (response) {
            if (response.data['redirect']) {
                document.location.href = response.data['redirect'];
            } else if (response.data['msorder']) {
                document.location.href = document.location.origin + document.location.pathname
                    + (document.location.search ? document.location.search + '&' : '?')
                    + 'msorder=' + response.data['msorder'];
            } else {
                location.reload();
            }
        }.bind(this);
        callbacks.submit.response.error = function (response) {
            setTimeout((function () {
                const elements = this.minishop.querySelectorsArray([this.order + ' button', this.order + ' a']);
                elements.forEach(el => el.disabled = false);
            }.bind(this)), 3 * this.minishop.timeout);

            const fields = document.querySelectorAll(this.order + ' [name]');
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
                        $field = document.querySelector(this.order + ' [name="' + key + '"]');
                    if ($field.type === 'checkbox' || $field.type === 'radio') {
                        $field.closest(this.inputParent).classList.add('error');
                    } else {
                        $field.classList.add('error');
                    }
                }
            }
        }.bind(this);
        return this.minishop.send(formData, this.callbacks.submit, this.minishop.Callbacks.Order.submit);
    }

    getrequired(value) {
        const callbacks = this.callbacks;
        callbacks.getrequired.response.success = function (response) {
            const fields = document.querySelectorAll(this.order + ' [name]'),
                requires = response.data['requires'];
            if (fields) {
                fields.forEach(el => {
                    el.classList.remove('required');
                    if (el.closest(this.inputParent)) {
                        el.closest(this.inputParent).classList.remove('required');
                    }
                });
            }

            for (var i = 0, length = requires.length; i < length; i++) {
                let field = document.querySelector(this.order + ' [name=' + requires[i] + ']');
                if (field) {
                    field.classList.add('required');
                    if (field.closest(this.inputParent)) {
                        field.closest(this.inputParent).classList.add('required');
                    }
                }
            }
        }.bind(this);
        callbacks.getrequired.response.error = function () {
            const fields = document.querySelectorAll(this.order + ' [name]');
            if (fields) {
                fields.forEach(el => {
                    el.classList.remove('required');
                    if (el.closest(this.inputParent)) {
                        el.closest(this.inputParent).classList.remove('required');
                    }
                });
            }
        }.bind(this);

        const formData = new FormData();
        formData.append('id', value);
        formData.append(this.minishop.actionName, 'order/getrequired');
        this.minishop.send(formData, this.callbacks.getrequired, this.minishop.Callbacks.Order.getrequired);
    }
}
