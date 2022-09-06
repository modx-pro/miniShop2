export default class CustomInputNumber {
    constructor(element, config) {

        if (!element) {
            console.error('Element is undefined.');
            return false;
        }

        this.element = element;

        this.defaults = {
            min: parseFloat(element.getAttribute('min')) || false,
            max: parseFloat(element.getAttribute('max')) || false,
            step: parseFloat(element.getAttribute('step')) || 1,
            placeholder: element.getAttribute('placeholder') || '',
            negative: element.dataset.negative
        }

        this.config = Object.assign({}, this.defaults, config);

        this.config.event = new Event('change', {
            bubbles: true,
            cancelable: true,
            composed: true,
        });

        this.markup = this.addMarkup();
        this.addListeners();
    }

    addMarkup() {
        const markup = {'wrapper': '', 'plus': '', 'field': '', 'minus': ''};
        markup.wrapper = this.element.closest('.input-number-wrap');
        markup.minus = markup.wrapper.querySelector('.input-number-minus');
        markup.field = this.element;
        markup.plus = markup.wrapper.querySelector('.input-number-plus');
        return markup;
    }

    addListeners() {
        this.markup.plus.addEventListener('click', () => this.numberUp());
        this.markup.minus.addEventListener('click', () => this.numberDown());
        this.markup.field.addEventListener('change', () => this.numberInput());
    }

    numberUp() {
        let config = this.config,
            field = this.markup.field,
            value = parseFloat(this.element.value);

        if (config.max >= value + config.step || !config.max) {
            this.element.value = value + config.step;
            field.value = this.element.value;

            this.triggerEvent(config);
        }
    }

    numberDown() {
        let config = this.config,
            field = this.markup.field,
            value = parseFloat(this.element.value),
            negative = config.min < 0 ? true : config.negative;

        if (config.min <= value - config.step || !config.min) {
            if (negative || value - config.step >= 0) {
                this.element.value = value - config.step;
            } else if (value - config.step < 0) {
                this.element.value = config.min ? config.min : 0;
            }
            field.value = this.element.value;

            this.triggerEvent(config);
        }
    }

    numberInput() {
        let config = this.config,
            field = this.markup.field,
            inputValue = parseFloat(field.value) || config.min;

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
        this.element.value = field.value;
    }

    triggerEvent(config) {
        this.element.dispatchEvent(config.event);
        if (window.jQuery !== 'undefined') {
            $(this.element).trigger('change');
        }
    }
}
