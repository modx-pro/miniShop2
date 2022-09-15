export default class CustomInputNumber {
    constructor(element, config) {

        if (!element) {
            throw new Error('Element is undefined.');
        }
        this.defaults = {
            wrapperSelector: '.ms-input-number-wrap',
            minusSelector: '.ms-input-number-minus',
            plusSelector: '.ms-input-number-plus',
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
        this.plus.addEventListener('click', this.numberUp.bind(this));
        this.minus.addEventListener('click', this.numberDown.bind(this));
        this.field.addEventListener('change', this.numberInput.bind(this));
    }

    numberUp() {
        let value = this.floatValue;

        if (this.config.max >= value + this.config.step || !this.config.max) {
            this.field.value = value + this.config.step;
            this.field.dispatchEvent(this.config.event);
        }
    }

    numberDown() {
        let value = this.floatValue,
            negative = this.config.min < 0 ? true : this.config.negative;

        if (this.config.min <= value - this.config.step || !this.config.min) {
            if (negative || value - this.config.step >= 0) {
                this.field.value = value - this.config.step;
            } else if (value - this.config.step < 0) {
                this.field.value = this.config.min || 0;
            }
            this.field.dispatchEvent(this.config.event);
        }
    }

    numberInput() {
        let inputValue = this.floatValue || this.config.min;

        if (inputValue % this.config.step !== 0) {
            inputValue = Math.round(inputValue / this.config.step) * this.config.step;
        }
        if (this.config.min && inputValue < this.config.min) {
            inputValue = this.config.min;
        }
        if (this.config.max && inputValue > this.config.max) {
            inputValue = this.config.max;
        }
        this.field.value = inputValue;
    }

    get floatValue(){
        return parseFloat(this.field.value);
    }
}
