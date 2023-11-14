

/**
 * This class is a input with more functionality.
 * It uses the Inputmask library to show a mask on the input.
 * Also, handles the events when change or blur.
 * It is usefull when you need and input for some numerical values and also need to give them some format.
 **/
class MaskedInput {
    /**
     * Associated input element for the component.
     * @type {HTMLElement}
     * @public
     */
    inputElement = null;

    /**
     * Callback invoked when the value changes.
     * @type {Function|null}
     * @public
     */
    onChangeCallback = null;

    /**
     * Callback invoked when the component loses focus.
     * @type {Function|null}
     * @public
     */
    onBlurCallback = null;

    /**
     * If an inputElement is not passed to the constructor, then, this property stores the attributes to
     * render the input element.
     * @type {Object}
     * @public
     */
    nodeAttributes = {};

    /**
     * Value associated with the editable component.
     * This is the real value. It is not the formatted value.
     * Notice that the value is always a string.
     * @type {string}
     * @private
     */
    _rawValue = null;

    /**
     * This is the type of the real value. It can be TYPE_CURRENCY, TYPE_PERCENTAGE, TYPE_DECIMAL, TYPE_INTEGER or TYPE_STRING
     * @type {string}
     * @public
     */
    valueType = MaskedInput.TYPE_DECIMAL;

    /**
     * This is the instance of the Inputmask class that is used to mask the input
     * @type {Inputmask}
     * @public
     */
    inputmask = null;

    /**
     * Define the options for the mask. By default is DEFAULT_DECIMAL_MASK_OPTIONS
     * @type {Object} 
     */
    maskOptions = MaskedInput.DEFAULT_DECIMAL_MASK_OPTIONS;

    /**
     * Define the default configuration for a mask of type currency
     */
    static DEFAULT_CURRENCY_MASK_OPTIONS = {
        alias: 'currency',
        groupSeparator: ' ',
        radixPoint: '.',
        rightAlign: true,
        suffix: ' €',
        prefix: '',
        placeholder: '0.00',
    };

    /**
     * Define the default configuration for a mask of type percentage
     */
    static DEFAULT_PERCENTAGE_MASK_OPTIONS = {
        alias: 'percentage',
        groupSeparator: ' ',
        radixPoint: '.',
        rightAlign: true,
        digits: 2,
        suffix: ' %',
        prefix: '',
        placeholder: '0,00',
    };

    /**
     * Define the default configuration for a mask of type decimal
     */
    static DEFAULT_DECIMAL_MASK_OPTIONS = {
        alias: 'decimal',
        groupSeparator: ' ',
        radixPoint: '.',
        rightAlign: true,
        digits: 2,
        suffix: '',
        prefix: '',
        placeholder: '0.00',
    };

    /**
     * Define the default configuration for a mask of type decimal
     */
    static DEFAULT_INTEGER_MASK_OPTIONS = {
        alias: 'integer',
        groupSeparator: ' ',
        radixPoint: '.',
        rightAlign: true,
        digits: 0,
        suffix: '',
        prefix: '',
        placeholder: '0',
    };

    /**
     * Define the default configuration for a mask of type Euros per Watts peak
     */
    static DEFAULT_PRICE_PER_WP_MASK_OPTIONS = {
        alias: 'numeric',
        groupSeparator: ' ',
        radixPoint: '.',
        autoGroup: true,
        rightAlign: true,
        digits: 4,
        suffix: ' €/Wp',
        prefix: '',
        placeholder: '0.00',
    };

    /**
     * Indicates the value for type currency
     */
    static TYPE_CURRENCY = "currency";

    /**
     * Indicates the value for type percentage
     */
    static TYPE_PERCENTAGE = "percentage";

    /**
     * Indicates the value for type decimal
     */
    static TYPE_DECIMAL = "decimal";

    /**
     * Indicates the value for type integer
     */
    static TYPE_INTEGER = "integer";

    /**
     * Indicates the value for type string
     */
    static TYPE_STRING = "string";

    /**
     * This is the name of the data attribute that will be used to store the instance of the component.
     */
    static INSTANCE = "maskedInputInstance";

    /**
     * This is the name of the class that will be used to identify the input element.
     */
    static MASKED_INPUT_CLASS = "masked-input";

    /**
     * Constructor for the MaskedInput class.
     * @param {Object} options - Configuration options for the component.
     * @param {HTMLElement} options.inputElement - Optional. Associated input element. If not passed, it will be created.
     * @param {Function|null} options.onChangeCallback - Value change callback.
     * @param {Function|null} options.onBlurCallback - Blur callback.
     * @param {Object} options.nodeAttributes - Node attributes for rendering.
     * @param {Object} options.maskOptions - Options to create the Inputmask instance if it is necesary
     * @param {*} options.value - Initial value of the component.
     */
    constructor(options) {
        Object.assign(this, options);

        if (!this.inputElement) {
            // We will construct the default element

            this.nodeAttributes = {
                ...options.nodeAttributes,
                ...{
                    "type": "text",
                },
            };

            this.inputElement = $(`<input/>`, this.nodeAttributes);
        }

        let originalInputVal = this.inputElement[0].value;

        // We will add the class to the input to make it identificable
        this.inputElement.addClass(MaskedInput.MASKED_INPUT_CLASS);

        if (!this.inputmask) {
            this.inputmask = new Inputmask(this.maskOptions ?? MaskedInput.DEFAULT_DECIMAL_MASK_OPTIONS);
        }

        this.inputmask.$el = this.inputElement;
        this.inputmask.mask(this.inputElement);

        // Stores the raw value from the input.
        this.value = originalInputVal;

        // Doing a cross reference
        this.inputElement.data(MaskedInput.INSTANCE, this);

        this.inputElement.change((event) => {
            let inputVal = event.currentTarget.inputmask.unmaskedvalue();
            if (this.valueType == MaskedInput.TYPE_PERCENTAGE) {
                // We will divide by 100 to get the real value
                inputVal /= 100;
            }
            this.value = inputVal;
            if (!event.data) {
                event.data = {};
            }
            // Lose the focus to hide the input
            this.inputElement.blur();

            event.data.newValue = this.value;
            this.onChangeCallback?.(event);
        });

        // set the listener to press enter and acts like a tab press
        this.inputElement.keydown((event) => {
            if (event.keyCode == 13 || event.keyCode == 9) {
                // We will simulate a tab press
                this.inputElement.blur();
            }
        });

        // Now we will add listeners to dispatch the patchEvent
        this.inputElement.blur((event) => {
            if (!event.data) {
                event.data = {};
            }
            event.data.newValue = this.value;
            this.onBlurCallback?.(event);
        });
    }

    /**
     * Sets a raw value for this instance and will show in the input the formatted version
     */
    set value(val) {
        this._rawValue = val;

        let showVal = this._rawValue
        if (this.valueType == MaskedInput.TYPE_PERCENTAGE) {
            showVal = this._rawValue * 100;
        }
        this.inputElement[0].value = showVal;

        this.inputmask.mask(this.inputElement);
    }


    /**
     * Gets the real number on the input if the type is number.
     * @returns {string} the raw value
     */
    get value() {
        // Removes separators and decimal characters. 
        return this._rawValue;
    }

    /**
     * Overrides the default validator to use the value of the component instead of the value of the input
     */
    static overrideValidator() {   
        $.validator.prototype.elementValue = function(element) {
            // Si el elemento tiene una instancia con un valor, usa ese valor
            if ($(element).data(MaskedInput.INSTANCE) instanceof MaskedInput) {
                return $(element).data(MaskedInput.INSTANCE).value;
            }
            // De lo contrario, usa el comportamiento predeterminado
            return $(element).val();
        };
    }

    static overrrideVal() {
        // Guarda el método original val() de jQuery
        var originalVal = $.fn.val;

        $.fn.val = function(value) {
            var maskedInputInstance = this.data(MaskedInput.INSTANCE);

            // Si el elemento tiene una instancia de MaskedInput
            if (maskedInputInstance) {
                if (typeof value !== 'undefined') {
                    // Setter
                    maskedInputInstance.value = value;
                    return this;
                } else {
                    // Getter
                    return maskedInputInstance.value.toString();
                }
            } else {
                // Si el elemento no tiene una instancia de MaskedInput, utiliza el método original val()
                return originalVal.apply(this, arguments);
            }
        };
    }
}

export default MaskedInput;