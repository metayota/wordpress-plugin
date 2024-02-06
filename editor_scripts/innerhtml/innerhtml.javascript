class RCInnerHtml extends Tag {
    render() {
        this.main.innerHTML = this.value
    }

    set value(v) {
        this._value = v
        if (this.main) {
            this.main.innerHTML = v
        }
    }

    get value() {
        return this._value
    }
}