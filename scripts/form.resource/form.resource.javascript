class ResourceFormTag extends Tag {
    setup() {
        this.autocomplete = false
    }
    init(){
        this._enabled = true
    }
    set target(t) {
        this._target = t
        this.update('this.target')
    }
    get target() {
        return this._target
    }
    set showdocumentation(s) {
        this._showdocumentation = s
        this.update('this.showdocumentation')
    }
    get showdocumentation() {
        return this._showdocumentation
    }
    set action(a) {
        this._action = a
        this.update('this.action')
    }
    get action() {
        return this._action
    }
    set method(m) {
        this._method = m
        this.update('this.method')
    }
    get method() {
        return this._method
    }
    set label(l) {
        this._label = l
        this.update('this.label')
    }
    get label() {
        return this._label
    }
    set enabled(e) {
        this._enabled = e
    }
    get enabled() {
        return this._enabled
    }
    set documentation(d) {
        this._documentation = d
        this.update('this.documentation')
    }
    get documentation() {
        return this._documentation
    }
    set subtitle(s) {
        this._subtitle = s
        this.update('this.subtitle')
    }
    isValid() {
        return this.parameterEl.isValid()
    }
    get subtitle() {
        return this._subtitle
    }
    set submitlabel(l) {
        this._submitlabel = l
        this.update('this.submitlabel')
    }
    get submitlabel() {
        return this._submitlabel
    }
    set cancellabel(c) {
        this._cancellabel = c 
        this.update('this.cancellabel')
    }
    get cancellabel() {
        return this._cancellabel
    }
    set resourcetype(r) {
        this._resourcetype = r
        if (r) {
            Resource.loaded(r).then( resource => {
                this.resource = resource;
                this.update('this.resource')
                this.update('this.resourcetype')
            })
        } else {
            this.resource = null
            this.update('this.resource')
            this.update('this.resourcetype')
        }
    }
    get resourcetype() {
        return this._resourcetype
    }
    set value(v) {
        this._value = v
        this.update('this.value')
    }
    get value() {
        return this._value
    }
    clear() {
        this.value = {}
    }
    reset() {
        this.value = {}
    }
    changed(event) {
        this.setAttribute('submitted', false)
        this._value = event;
        this.fire('change',event)
    }
    cancelClicked() {
        this.value = {}
        this.fire('cancel')
    }
    saveClicked() {
        if (!this.enabled || (this.submitted === true)) {
            return;
        }
        //this.setAttribute('submitted', true)
        Resource.loaded(this._resourcetype).then( resource => {
            if (this.isValid()) {
                let obj = Resource.create(this._resourcetype)
                if (obj instanceof Tag) {
                    obj = {}
                }
                Object.assign(obj, this.parameterEl.value)
                this.fire('submit', obj)
                if (!!this.action) {
                    this.form.submit()
                }
            }
        })
    }
    
}