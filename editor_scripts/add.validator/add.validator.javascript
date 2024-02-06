class AddValidator extends Tag {
    tagTypeChanged(tagtype) {
        if (this.validatorOptions) {
            this.validatorOptions.resourcetype = tagtype
        }
		this.tagtype = tagtype
    }

    set name(n) {
        this._name = n
        this.tagTypeChanged(n)
        this.update('this.name')
    }

    get name() {
        return this._name
    }

    set options(o) {
        this._options = o
        this.update('this.options')
    }
    get options() {
        return this._options
    }
    submit(event) {
        this.fire('submit',{name:this.tagtypename.value, options:event})
    }
}