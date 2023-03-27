export default class MiniShop {
    constructor(config) {
        this.config = Object.assign(config, {
            actionUrl: document.location.href,
            formMethod: 'POST',
        });
        this.config.callbacksObjectTemplate = this.callbacksObjectTemplate;
        this.Callbacks = this.config.Callbacks = {
            Cart: {
                add: this.config.callbacksObjectTemplate(),
                remove: this.config.callbacksObjectTemplate(),
                change: this.config.callbacksObjectTemplate(),
                clean: this.config.callbacksObjectTemplate(),
            },
            Order: {
                add: this.config.callbacksObjectTemplate(),
                getcost: this.config.callbacksObjectTemplate(),
                clean: this.config.callbacksObjectTemplate(),
                submit: this.config.callbacksObjectTemplate(),
                getrequired: this.config.callbacksObjectTemplate(),
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
        this.initialize();
    }

    async setHandler(property, pathPropertyName, classnamePropertyName, defaultPath, defaultClassName, errorMsg, response) {
        const classPath = (this.config.hasOwnProperty(pathPropertyName) && this.config[pathPropertyName]) ?
                this.config[pathPropertyName] : defaultPath,
            className = (this.config.hasOwnProperty(classnamePropertyName) && this.config[classnamePropertyName]) ?
                this.config[classnamePropertyName] : defaultClassName,
            config = response ? response[className] : this;

        try {
            const { default: ModuleName } = await import(classPath);
            this[property] = new ModuleName(config);
        } catch (e) {
            console.error(e, errorMsg);
        }
    }

    async initialize() {
        this.setHandler(
            'Cart',
            'cartClassPath',
            'cartClassName',
            './mscart.class.js',
            'MsCart',
            'Произошла ошибка при загрузке модуля корзины');

        this.setHandler(
            'Order',
            'orderClassPath',
            'orderClassName',
            './msorder.class.js',
            'MsOrder',
            'Произошла ошибка при загрузке модуля отправки заказа');

        if (this.config.notifySettingsPath) {
            const response = await this.sendResponse({ url: this.config.notifySettingsPath, method: 'GET' });
            if (response.ok) {
                const messageSettings = await response.json();
                if (messageSettings) {
                    this.setHandler(
                        'Message',
                        'notifyClassPath',
                        'notifyClassName',
                        './msnotify.class.js',
                        'MsNotify',
                        'Произошла ошибка при загрузке модуля уведомлений',
                        messageSettings);
                }
            }
        }

        document.addEventListener('submit', e => {
            e.preventDefault();
            const form = e.target;
            const action = form.querySelector(this.action) ? form.querySelector(this.action).value : '';

            if (action) {
                const formData = new FormData(form),
                    components = this.getObjectMethod(action);
                formData.append(this.actionName, action);
                this.formData = formData;
                this[components.object][components.method](this.formData);
            }
        });
    }

    getObjectMethod(action) {
        const actionComponents = action.split('/'),
            object = actionComponents[0].replace(actionComponents[0].substring(0, 1), actionComponents[0].substring(0, 1).toUpperCase()),
            method = actionComponents[1];
        return {object, method};
    }

    callbacksObjectTemplate() {
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
            headers = params.headers || { 'X-Requested-With': 'XMLHttpRequest' },
            url = params.url || this.config.actionUrl,
            method = params.method || this.config.formMethod;

        let options = { method, headers, body };
        if (method === 'GET') {
            options = { method, headers };
        }

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
                value: this.config.ctx,
            });
        } else if (data instanceof FormData) {
            data.append('ctx', this.config.ctx);
        } else if (typeof data === 'string') {
            data += '&ctx=' + this.config.ctx;
        }

        const response = await this.sendResponse({ body: data, headers });
        if (response.ok) {
            const result = await response.json();
            if (result.success) {
                this.runCallback(callbacks.response.success, this, result);
                this.runCallback(userCallbacks.response.success, this, result);
                result.message ? this.Message.success(result.message) : '';
            } else {
                this.runCallback(callbacks.response.error, this, result);
                this.runCallback(userCallbacks.response.error, this, result);
                result.message ? this.Message.error(result.message) : '';
            }
            this.runCallback(callbacks.ajax.done, this, response);
            this.runCallback(userCallbacks.ajax.done, this, response);
        } else {
            this.runCallback(callbacks.ajax.fail, this, response);
            this.runCallback(userCallbacks.ajax.fail, this, response);
        }
        this.runCallback(callbacks.ajax.always, this, response);
        this.runCallback(userCallbacks.ajax.always, this, response);
    }

    formatPrice(price) {
        const pf = this.config.price_format;
        price = this.numberFormat(price, pf[0], pf[1], pf[2]);

        if (this.config.price_format_no_zeros && pf[0] > 0) {
            price = price.replace(/(0+)$/, '');
            price = price.replace(/[^0-9]$/, '');
        }

        return price;
    }

    formatWeight(weight) {
        const wf = this.config.weight_format;
        weight = this.numberFormat(weight, wf[0], wf[1], wf[2]);

        if (this.config.weight_format_no_zeros && wf[0] > 0) {
            weight = weight.replace(/(0+)$/, '');
            weight = weight.replace(/[^0-9]$/, '');
        }

        return weight;
    }

    numberFormat(number, decimals, decPoint, thousandsSep) {
        // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
        // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // bugfix by: Michael White (http://crestidg.com)
        var i, j, kw, kd, km;

        // input sanitation & defaults
        if (isNaN(decimals = Math.abs(decimals))) {
            decimals = 2;
        }
        if (decPoint == undefined) {
            decPoint = ',';
        }
        if (thousandsSep == undefined) {
            thousandsSep = '.';
        }

        i = parseInt(number = (+number || 0).toFixed(decimals)) + '';

        if ((j = i.length) > 3) {
            j = j % 3;
        } else {
            j = 0;
        }

        km = j
            ? i.substring(0, j) + thousandsSep
            : '';
        kw = i.substring(j).replace(/(\d{3})(?=\d)/g, "$1" + thousandsSep);
        kd = (decimals
            ? decPoint + Math.abs(number - i).toFixed(decimals).replace(/-/, '0').slice(2)
            : '');

        return km + kw + kd;
    }
}
