import msNotify from "./msnotify.class.js";
export default class msSwal2 extends msNotify{
    success(message){
        if (window[this.config.handlerClassName]) {
            const options = Object.assign(this.config.handlerOptions, {icon: 'success', title: message});
            window[this.config.handlerClassName]['fire'](options);
        }
    }
    error(message) {
        if (window[this.config.handlerClassName]) {
            const options = Object.assign(this.config.handlerOptions, {icon: 'error', title: message});
            window[this.config.handlerClassName]['fire'](options);
        }
    }
    info(message) {
        if (window[this.config.handlerClassName]) {
            const options = Object.assign(this.config.handlerOptions, {icon: 'info', title: message});
            window[this.config.handlerClassName]['fire'](options);
        }
    }
}
