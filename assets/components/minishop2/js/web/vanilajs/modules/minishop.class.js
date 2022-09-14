export default class MiniShop {
    constructor(miniShop2Config) {
        this.miniShop2Config = miniShop2Config;
        this.miniShop2Config.callbacksObjectTemplate = this.callbacksObjectTemplate;
        this.Callbacks = this.miniShop2Config.Callbacks = {
            Cart: {
                add: this.miniShop2Config.callbacksObjectTemplate(),
                remove: this.miniShop2Config.callbacksObjectTemplate(),
                change: this.miniShop2Config.callbacksObjectTemplate(),
                clean: this.miniShop2Config.callbacksObjectTemplate()
            },
            Order: {
                add: this.miniShop2Config.callbacksObjectTemplate(),
                getcost: this.miniShop2Config.callbacksObjectTemplate(),
                clean: this.miniShop2Config.callbacksObjectTemplate(),
                submit: this.miniShop2Config.callbacksObjectTemplate(),
                getrequired: this.miniShop2Config.callbacksObjectTemplate()
            },
        };
        this.Callbacks.add = this.addCallback.bind(this);
        this.Callbacks.remove = this.removeCallback.bind(this);
        this.actionName = 'ms2_action';
        this.action = '[type="submit"][name=' + this.actionName + ']';
        this.form = '.ms2_form';
        this.formData = null;
        this.Message = null;
        this.timeout = 300;
        if (!this.miniShop2Config.actionUrl) {
            this.miniShop2Config.actionUrl = document.location.href;
        }
        if (!this.miniShop2Config.formMethod) {
            this.miniShop2Config.formMethod = 'post';
        }
        this.initialize();
    }

    async setHandler(property, pathPropertyName, classnamePropertyName, defaultPath, defaultClassName, error_msg, response) {
        const classPath = (this.miniShop2Config.hasOwnProperty(pathPropertyName) && this.miniShop2Config[pathPropertyName]) ?
                this.miniShop2Config[pathPropertyName] : defaultPath,
            className = (this.miniShop2Config.hasOwnProperty(classnamePropertyName) && this.miniShop2Config[classnamePropertyName]) ?
                this.miniShop2Config[classnamePropertyName] : defaultClassName,
            config = response ? response[className] : this;

        try {
            const {default: ModuleName} = await import(classPath);
            this[property] = new ModuleName(config);
        } catch (e) {
            console.error(e, error_msg);
        }
    }

    async initialize() {

        this.setHandler(
            'Cart',
            'cartClassPath',
            'cartClassName',
            './mscart.class.js',
            'msCart',
            'Произошла ошибка при загрузке модуля корзины');

        this.setHandler(
            'Order',
            'orderClassPath',
            'orderClassName',
            './msorder.class.js',
            'msOrder',
            'Произошла ошибка при загрузке модуля отправки заказа');

        if (this.miniShop2Config.notifySettingsPath) {
            const response = await this.sendResponse({url: this.miniShop2Config.notifySettingsPath});
            if (response.ok) {
                const messageSettings = await response.json();
                if(messageSettings){
                    this.setHandler(
                        'Message',
                        'notifyClassPath',
                        'notifyClassName',
                        './msnotify.class.js',
                        'msNotify',
                        'Произошла ошибка при загрузке модуля уведомлений',
                        messageSettings);
                }
            }
        }

        document.addEventListener('submit', e => {
            e.preventDefault();
            const $form = e.target,
                action = $form.querySelector(this.action) ? $form.querySelector(this.action).value : '';

            if (action) {
                const formData = new FormData($form);
                formData.append(this.actionName, action);
                this.formData = formData;

                this.controller(action);
            }
        });
    }

    controller(action) {
        switch (action) {
            case 'cart/add':
                this.Cart.add(this.formData);
                break;
            case 'cart/remove':
                this.Cart.remove(this.formData);
                break;
            case 'cart/change':
                this.Cart.change(this.formData);
                break;
            case 'cart/clean':
                this.Cart.clean(this.formData);
                break;
            case 'order/submit':
                this.Order.submit(this.formData);
                break;
            case 'order/clean':
                this.Order.clean(this.formData);
                break;
            default:
                return;
        }
    }

    callbacksObjectTemplate() {
        return {
            // return false to prevent send data
            before: [],
            response: {
                success: [],
                error: []
            },
            ajax: {
                done: [],
                fail: [],
                always: []
            }
        }
    }

    addCallback(path, name, func) {
        if (typeof func !== 'function') {
            return false;
        }
        path = path.split('.');
        let obj = this.Callbacks;
        for (let i = 0; i < path.length; i++) {
            if (obj[path[i]] === undefined) {
                return false;
            }
            obj = obj[path[i]];
        }
        if (typeof obj !== 'object') {
            obj = [obj];
        }
        if (name !== undefined) {
            obj[name] = func;
        } else {
            obj.push(func);
        }
        return true;
    }

    removeCallback(path, name) {
        path = path.split('.');
        let obj = this.Callbacks;
        for (let i = 0; i < path.length; i++) {
            if (obj[path[i]] === undefined) {
                return false;
            }
            obj = obj[path[i]];
        }
        if (obj[name] !== undefined) {
            delete obj[name];
            return true;
        }
        return false;
    }

    runCallback(callback, bind) {
        if (typeof callback === 'function') {
            return callback.apply(bind, Array.prototype.slice.call(arguments, 2));
        } else if (typeof callback === 'object') {
            for (let i in callback) {
                if (callback.hasOwnProperty(i)) {
                    const response = callback[i].apply(bind, Array.prototype.slice.call(arguments, 2));
                    if (response === false) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    sendResponse(params) {
        const body = params.body || new FormData(),
            headers = params.headers || {"X-Requested-With": "XMLHttpRequest"},
            url = params.url || this.miniShop2Config.actionUrl,
            method = params.method || this.miniShop2Config.formMethod;

        const options = {method, headers, body};

        return fetch(url, options);
    }

    async send(data, callbacks, userCallbacks, headers) {
        // callback before
        if (this.runCallback(callbacks.before) === false || this.runCallback(userCallbacks.before) === false) {
            return;
        }

        if (Array.isArray(data)) {
            data.push({
                name: 'ctx',
                value: this.miniShop2Config.ctx
            });
        } else if (data instanceof FormData) {
            data.append('ctx', this.miniShop2Config.ctx);
        } else if (typeof data == 'string') {
            data += '&ctx=' + this.miniShop2Config.ctx;
        }

        const xhr = await this.sendResponse({body : data, headers});
        const self = this;
        if (xhr.ok) {
            const response = await xhr.json();
            if (response.success) {
                if (response.message) {
                    self.Message.success(response.message);
                }
                self.runCallback(callbacks.response.success, self, response);
                self.runCallback(userCallbacks.response.success, self, response);
            } else {
                self.Message.error(response.message);
                this.runCallback(callbacks.response.error, self, response);
                this.runCallback(userCallbacks.response.error, self, response);
            }
            this.runCallback(callbacks.ajax.done, self, xhr);
            this.runCallback(userCallbacks.ajax.done, self, xhr);
        } else {
            this.runCallback(callbacks.ajax.fail, self, xhr);
            this.runCallback(userCallbacks.ajax.fail, self, xhr);
        }
        this.runCallback(callbacks.ajax.always, self, xhr);
        this.runCallback(userCallbacks.ajax.always, self, xhr);
    }

    formatPrice(price) {
        var pf = this.miniShop2Config.price_format;
        price = this.number_format(price, pf[0], pf[1], pf[2]);

        if (this.miniShop2Config.price_format_no_zeros && pf[0] > 0) {
            price = price.replace(/(0+)$/, '');
            price = price.replace(/[^0-9]$/, '');
        }

        return price;
    }

    formatWeight(weight) {
        var wf = this.miniShop2Config.weight_format;
        weight = this.number_format(weight, wf[0], wf[1], wf[2]);

        if (this.miniShop2Config.weight_format_no_zeros && wf[0] > 0) {
            weight = weight.replace(/(0+)$/, '');
            weight = weight.replace(/[^0-9]$/, '');
        }

        return weight;
    }

    number_format(number, decimals, dec_point, thousands_sep) {
        // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
        // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // bugfix by: Michael White (http://crestidg.com)
        var i, j, kw, kd, km;

        // input sanitation & defaults
        if (isNaN(decimals = Math.abs(decimals))) {
            decimals = 2;
        }
        if (dec_point == undefined) {
            dec_point = ',';
        }
        if (thousands_sep == undefined) {
            thousands_sep = '.';
        }

        i = parseInt(number = (+number || 0).toFixed(decimals)) + '';

        if ((j = i.length) > 3) {
            j = j % 3;
        } else {
            j = 0;
        }

        km = j
            ? i.substr(0, j) + thousands_sep
            : '';
        kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
        kd = (decimals
            ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, '0').slice(2)
            : '');

        return km + kw + kd;
    }
}
