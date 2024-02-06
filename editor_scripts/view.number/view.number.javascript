class ViewNumber extends Tag {
    setup() {
        this.decimal_separator = translate('number_format_decimal_separator').length == 1 ? translate('number_format_decimal_separator') : '.';
        this.group_separator = translate('number_format_group_separator').length == 1 ? translate('number_format_group_separator') : ' ';
        this.group_separator_characters_count = 3;
        this.suffixSeparator = ' ';
    }
    getSuffix() {
        if (this.suffix && this.suffix != '') {
            return this.suffixSeparator + this.suffix;
        } else {
            return '';
        }
    }
    set value(v) {
        if (v == undefined) {
            v = ''
        }
        this._value = v
        this.update('this.value')
        let formattedValue = this.valueToDisplayValue(v)
        this.setAttribute('formattedValue',formattedValue)
    }
    get value() {
        return this._value
    }
    set suffix(v) {
        this._suffix = v
        this.update('this.suffix')
        let formattedValue = this.valueToDisplayValue(v)
        this.setAttribute('formattedValue',formattedValue)
    }
    get suffix() {
        return this._suffix
    }
 updateValueDisplay(inputString, input = null, keepSelection = true) {
        const inputValue = String(inputString);

        
        
        let processedValue = '';
        let processedNumber = 0;
        let integerPart = inputValue.indexOf(this.decimal_separator) === -1 ? true : false;

        for (let i = inputValue.length - 1; i >= 0; i--) {
            const charAtIndex = inputValue.charAt(i);
            if (charAtIndex === this.decimal_separator) {
                processedNumber = -1;
                integerPart = true;
            }
            const shouldBeSeparator = integerPart && (((processedNumber % this.group_separator_characters_count) === 0) && processedNumber > 0);

            if (shouldBeSeparator && !processedValue.startsWith(this.group_separator)) {
                processedValue = this.group_separator + processedValue;
            }
            let decimal_separator = this.decimal_separator;
            let regex = new RegExp(`[\\d\\${decimal_separator}]`, 'g');

            if (charAtIndex.match(regex)) {
                processedValue = charAtIndex + processedValue;
                processedNumber++;
            }

        }

        return processedValue;
    }
    valueToDisplayValue(value) {
        if (!this.isNumberInRange(value)) {
            value = this.getNumberInRange(value) + '';
        }
        let valueToDisplay = String(value);


        valueToDisplay = this.checkAndCorrectFractonSize(valueToDisplay);

        let newValue = this.updateValueDisplay(valueToDisplay)
        let suffix = this.getSuffix();
        

        return newValue + suffix;
    }
    
    checkAndCorrectFractonSize(value) {
        if (this.decimal_places !== null && (this.decimal_places === null || this.decimal_places === 0 || value.endsWith(this.decimal_separator))) {
            return Math.round(value) + '';
        } else {
            return (value * 1).toFixed(this.decimal_places * 1).replace('.', this.decimal_separator);
        }
    }
     isNumberInRange(value) {
        if (value !== null) {
            if (this.min !== null) {
                if (value < this.min) {
                    return false;
                }
            }
            if (this.max !== null) {
                if (value > this.max) {
                    return false;
                }
            }
        }
        return true;
    }
    getNumberInRange(value) {
        if (value !== null) {
            if (this.min !== null) {
                if (value < this.min) {
                    return this.min;
                }
            }
            if (this.max !== null) {
                if (value > this.max) {
                    return this.max;
                }
            }
        }

        return value;
    }
}