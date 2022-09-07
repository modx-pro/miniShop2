export default class CustomInputNumber {
    constructor(element, config) {

        if (!element) {
            console.error('Element is undefined.');
            return false;
        }
        this.defaults = {
            wrapperSelector: '.input-number-wrap',
            minusSelector: '.input-number-minus',
            plusSelector: '.input-number-plus',
            min: parseFloat(element.getAttribute('min')) || 0,
            max: parseFloat(element.getAttribute('max')) || false,
            step: parseFloat(element.getAttribute('step')) || 1,
            negative: element.dataset.negative
        }

        this.config = Object.assign({}, this.defaults, config);

        this.wrapper = element.closest(this.config.wrapperSelector);
        if (!this.wrapper) return false;
        this.plus = this.wrapper.querySelector(this.config.plusSelector);
        this.minus = this.wrapper.querySelector(this.config.minusSelector);
        this.field = element;

        this.config.event = new Event('change', {
            bubbles: true,
            cancelable: true,
            composed: true,
        });

        this.addListeners();
    }

    addListeners() {
        this.plus.addEventListener('click', () => this.numberUp());
        this.minus.addEventListener('click', () => this.numberDown());
        this.field.addEventListener('change', () => this.numberInput());
    }

    numberUp() {
        let config = this.config,
            field = this.field,
            value = parseFloat(this.field.value);

        if (config.max >= value + config.step || !config.max) {
            field.value = value + config.step;
            this.triggerEvent(config);
        }
    }

    numberDown() {
        let config = this.config,
            field = this.field,
            value = parseFloat(this.field.value),
            negative = config.min < 0 ? true : config.negative;

        if (config.min <= value - config.step || !config.min) {
            if (negative || value - config.step >= 0) {
                field.value = value - config.step;
            } else if (value - config.step < 0) {
                field.value = config.min ? config.min : 0;
            }
            this.triggerEvent(config);
        }
    }

    numberInput() {
        let config = this.config,
            field = this.field,
            inputValue = parseFloat(this.field.value) || config.min;

        if (inputValue % config.step !== 0) {
            inputValue = Math.round(inputValue / config.step) * config.step;
        }
        if (config.min && inputValue < config.min) {
            inputValue = config.min;
        }
        if (config.max && inputValue > config.max) {
            inputValue = config.max;
        }
        field.value = inputValue;
    }

    triggerEvent(config) {
        this.field.dispatchEvent(config.event);
        if (window.jQuery !== 'undefined') {
            $(this.field).trigger('change');
        }
    }
}
