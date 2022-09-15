import msNotify from "./msnotify.class.js";

export default class msIziToast extends msNotify {
    show(type, message) {
        type = type || 'error';
        if (window[this.config.handlerClassName] && message) {
            const options = Object.assign(this.config.handlerOptions, {title: message});
            try {
                window[this.config.handlerClassName][type](options);
            } catch (e) {
                console.error(e, 'Не найден метод ' + type + ' в классе ' + this.config.handlerClassName);
            }
        }
    }
}
