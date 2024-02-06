class RCFormElement extends Tag {
    setup() {
        this.typesCls = Resource.cls('parametertype') 
        this.types = this.typesCls.types
    }
    init() {
        this.fire('register',this)
    }
    get attributesForElement() {
        let obj = Object.assign( {}, this.options, { name: this.name, label: this.label, value: this.value, validators: this.validators, required: this.required } );
        return obj;
    }
    resourceChanged(v) {
        this.fire('change',v)
    }
    set options(o) {
        this._options = o
        this.update('this.options')
        this.update('this.attributesForElement')
    }
    get options() {
        return this._options
    }
    set parameters(v) {
        this._parameters = v
    }
    get parameters() {
        return this._parameters
    }
    set type(v) {
        this._type = v

        this.resolvedType = this.typesCls.getTypeByName(v)
        
        if (this.resolvedType) {
			// ok
        } else {
            console.warn('form.element type could not be resolved',v)
        }
    } 
    get type() {
        return this._type
    }
    set value(v) {
        this._value = v
        this.update('this.value')
        this.update('this.attributesForElement')
    }
    get value() {
        if (this.include && this.include.renderedTag && this.include.renderedTag.tag) {
            return this.include?.renderedTag?.tag?.value
        } else {
            return this._value
        }
    }
    isValid() {
        if (!this.include.renderedTag.tag.isValid) {
            return true;
        }
        return this.include.renderedTag.tag.isValid()
    }
    set name(n) {
        this._name = n
        this.update('this.name')
        this.update('this.attributesForElement')
    }
    get name() {
        return this._name
    }
    set label(l) {
        this._label = l
        this.update('this.label')
        this.update('this.attributesForElement')
    }
    get label() {
        return this._label
    }
}