import msNotify from "./msnotify.class.js";
export default class msSwal2 extends msNotify{

    showMessage(options){
        if (window[this.config.handlerClassName]) {
            options = Object.assign(this.config.handlerOptions, options);
            window[this.config.handlerClassName]['fire'](options);
        }
    }

    success(message){
        this.showMessage({icon: 'success', title: message});
    }
    error(message) {
        this.showMessage({icon: 'error', title: message});
    }
    info(message) {
        this.showMessage({icon: 'info', title: message});
    }
}
