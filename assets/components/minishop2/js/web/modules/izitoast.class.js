import msNotify from "./msnotify.class.js";

export default class msIziToast extends msNotify{
    success(message){
        if (window[this.config.handlerClassName]) {
            const options = Object.assign(this.config.handlerOptions, {title: message});
            window[this.config.handlerClassName]['success'](options);
        }
    }
    error(message) {
        if (window[this.config.handlerClassName]) {
            const options = Object.assign(this.config.handlerOptions, {title: message});
            window[this.config.handlerClassName]['error'](options);
        }
    }
    info(message) {
        if (window[this.config.handlerClassName]) {
            const options = Object.assign(this.config.handlerOptions, {title: message});
            window[this.config.handlerClassName]['info'](options);
        }
    }
}
