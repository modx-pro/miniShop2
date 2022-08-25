import msNotify from "./msnotify.class.js";

export default class msIziToast extends msNotify{
    showMessage(type, message){
        if (window[this.config.handlerClassName]) {
            const options = Object.assign(this.config.handlerOptions, {title: message});
            try{
                window[this.config.handlerClassName][type](options);
            }catch(e){
                console.error(e, 'Не найден метод ' + type + ' в классе ' + this.config.handlerClassName);
            }
        }
    }

    success(message){
        this.showMessage('success', message);
    }
    error(message) {
        this.showMessage('error', message);
    }
    info(message) {
        this.showMessage('info', message);
    }
}
