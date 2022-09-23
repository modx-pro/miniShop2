import MsNotify from './msnotify.class.js';

export default class MsSwal2 extends MsNotify {
    show(type, message) {
        if (window[this.config.handlerClassName] && message) {
            const options = Object.assign(this.config.handlerOptions, {icon: type, title: message});
            window[this.config.handlerClassName]['fire'](options);
        }
    }
}
