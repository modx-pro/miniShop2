export default class msNotify {
    constructor(config) {
        this.config = config;
        this.initialize();
    }

    initialize() {
        if (this.config.jsPath) {
            const script = document.createElement('script');
            script.src = this.config.jsPath;
            script.async = true;
            document.body.appendChild(script);
        }

        if (this.config.cssPath) {
            const styles = document.createElement('link');
            styles.href = this.config.cssPath;
            styles.rel = 'stylesheet';
            document.head.appendChild(styles);
        }
    }

    close() {}

    show(message) {
        if (message !== '') {
            alert(message);
        }
    }

    success(message) {
    }

    error(message) {
    }

    info(message) {
    }
}
