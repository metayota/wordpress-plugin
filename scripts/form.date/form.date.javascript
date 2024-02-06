class FormDate extends Tag {
    set value(v) {
        this._value = v
        this.update('this.value')
    }
    get value() {
        return this._value
    }
    set label(l) {
        this._label = l
        this.update('this.label')
    }
    get label() {
        return this._label
    }
    dateChanged(date) {
        this._value = date
        this.fire('change',date)
    }
}