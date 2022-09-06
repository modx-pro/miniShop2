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
        const markup = {'wrapper': '', 'plus': '', 'field': '', 'minus': ''},
            rowmarkup = {
                wrapper: {
                    tagName: 'div',
                    classNames: ['input-number-wrap'],
                    type: ''
                },
                plus: {
                    tagName: 'button',
                    classNames: ['input-number-plus', 'input-number-btn', 'btn', 'btn-sm', 'btn-secondary'],
                    type: 'button'
                },
                field: {
                    tagName: 'input',
                    classNames: ['input-number-emulator'],
                    type: 'text',
                    value: parseFloat(this.element.value) || 0,
                    placeholder: this.config.placeholder
                },
                minus: {
                    tagName: 'button',
                    classNames: ['input-number-minus', 'input-number-btn', 'btn', 'btn-sm', 'btn-secondary'],
                    type: 'button'
                }
            };


        this.element.classList.add('input-visually-hidden');
        for (let k in rowmarkup) {
            markup[k] = this.createElement(rowmarkup[k]);
        }
        markup.wrapper.appendChild(markup.minus);
        markup.wrapper.appendChild(markup.field);
        markup.wrapper.appendChild(markup.plus);
        this.element.after(markup.wrapper);
        return markup;
    }

    createElement(tagConfig) {
        let tag = document.createElement(tagConfig.tagName);
        tagConfig.classNames.map(name => tag.classList.add(name));
        if (tagConfig.placeholder) {
            tag.placeholder = tagConfig.placeholder;
        }
        if (tagConfig.value) {
            tag.value = tagConfig.value;
        }
        if (tagConfig.type) {
            tag.type = tagConfig.type;
        }
        return tag;
    }

    addListeners() {
        const $this = this;
        $this.markup.plus.addEventListener('click', () => $this.numberUp());
        $this.markup.minus.addEventListener('click', () => $this.numberDown());
        $this.markup.field.addEventListener('change', () => $this.numberInput());
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
            inputValue = parseFloat(field.value) || config.min,
            oldValue = this.element.value;

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

        if (oldValue !== inputValue) {
            this.triggerEvent(config);
        }
    }

    triggerEvent(config) {
        this.element.dispatchEvent(config.event);
        if (window.jQuery !== 'undefined') {
            $(this.element).trigger('change');
        }
    }
}
