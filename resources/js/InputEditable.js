

/**
 * This class will update one value using a PATCH service.
 * It will call the service on ENTER press, blurout, TAB press.
 * It will show a loading animation.
 * It will show an error message if necesssary
 *  
 **/
class InputEditable {
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
     * Attributes of the node used to render the component.
     * @type {Object}
     * @public
     */
    nodeAttributes = {};

    /**
     * Value associated with the editable component.
     * This is the abstract value (the real value). It is not the formatted string.
     * @type {string}
     * @private
     */
    _rawValue = null;

    /**
     * This is the type of the real value. It can be "string", "integer", "decimal", "percentage", "currency"
     * @type {string}
     * @public
     */
    valueType = InputEditable.TYPE_STRING;

    /**
     * Determine if the input will show a mask or not.
     * @type {Inputmask}
     * @public
     */
    inputmask = null;

    /**
     * Define the options for the mask
     * @type {Object} 
     */
    maskOptions = InputEditable.DEFAULT_DECIMAL_MASK_OPTIONS;

    /**
     * Define the default configuration for a mask of type currency
     */
    static DEFAULT_CURRENCY_MASK_OPTIONS = {
        alias: 'currency',
        groupSeparator: ' ',
        radixPoint: '.',
        autoGroup: true,
        rightAlign: true,
        digits: 2,
        suffix: ' €',
        prefix: '',
        placeholder: '0.00',
    };

    /**
     * Define the default configuration for a mask of type percentage
     */
    static DEFAULT_PERCENTAGE_MASK_OPTIONS = {
        alias: 'numeric',
        groupSeparator: ' ',
        radixPoint: '.',
        autoGroup: true,
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
        alias: 'numeric',
        groupSeparator: ' ',
        radixPoint: '.',
        autoGroup: true,
        rightAlign: true,
        digits: 2,
        suffix: '',
        prefix: '',
        placeholder: '0.00',
    };

    /**
     * Define the default configuration for a mask of type Euros per Watts peak
     */
    static DEFAULT_PRICE_PER_WP_MASK_OPTIONS = {
        alias: 'numeric',
        groupSeparator: '',
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
     * Constructor for the InputEditable class.
     * @param {Object} options - Configuration options for the component.
     * @param {HTMLElement} options.inputElement - Associated input element.
     * @param {Function|null} options.onChangeCallback - Value change callback.
     * @param {Function|null} options.onBlurCallback - Blur callback.
     * @param {string} options.nodeType - Type of node for rendering.
     * @param {Object} options.nodeAttributes - Node attributes for rendering.
     * @param {Object} options.maskOptions - Options to create the Inputmask instance if it is necesary
     * @param {*} options.value - Initial value of the component.
     */
    constructor(options) {
        Object.assign(this, options);

        if (!this.inputElement) {
            // We will construct the default element

            this.nodeAttributes = {
                ...{
                    "type": "text",
                    "class": `content-editable ${this.additionalClasses ?? ''}`,
                },
                ...options.nodeAttributes
            };

            this.inputElement = $(`<input/>`, this.nodeAttributes);
        }

        if (!this.inputmask) {
            this.inputmask = new Inputmask(this.maskOptions ?? InputEditable.DEFAULT_DECIMAL_MASK_OPTIONS);
            this.inputmask.$el = this.inputElement;
            this.inputmask.mask(this.inputElement);
        }

        // Doing a cross reference
        this.inputElement.data("InputEditableInstance", this);
        this.value = this.nodeAttributes.value;

        this.inputElement.change((event) => {
            let inputVal = event.currentTarget.inputmask.unmaskedvalue();
            if (this.valueType == InputEditable.TYPE_PERCENTAGE) {
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
                event.preventDefault();
                event.stopPropagation();
                // We will simulate a tab press
                this.inputElement.blur();

                // Obtén todos los elementos de la clase
                var allElements = this.inputElement.closest("table").find("input.content-editable");

                // Obtén el índice del elemento actual
                var currentIndex = allElements.index(this.inputElement);

                // Obtén el índice del siguiente elemento
                var nextIndex = currentIndex + (event.shiftKey && event.keyCode == 9 ? - 1 : 1);

                allElements.eq(nextIndex).focus().select();
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
        if (this.valueType == "percentage") {
            showVal = this._rawValue * 100;
        }
        this.inputElement.val(showVal);

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
}

export default InputEditable;