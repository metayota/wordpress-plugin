class ResParameter extends Tag {

    setup() {
        this.value = {}  
        this._elements = [] 
    }

    set elements(e) {
        this._elements = e
    }

    get elements() {
        return this._elements
    }

    set showdocumentation(s) {
        this._showdocumentation = s
        this.update('this.showdocumentation')
    }

    get showdocumentation() {
        return this._showdocumentation
    }

    set documentation(d) {
        this._documentation = d
        this.update('this.documentation')
    }


    get documentation() {
        return this._documentation
    }
    
    set tag(tag) {
        this._tag = tag;
        this._elements = []
        this.parameters = this.getParameters()
        this.update('this.parameters')
    }
    
    get tag() {
        return this._tag;
    }
    
    parameterChanged(parameterName,value) {
        if (this._value === undefined || this._value === null) {
            this._value = {}
        }
        this._value[parameterName] = value
        this.fire('change',this.value)
    }

    isValid() {
        let valid = true
        
        for(let element of this.elements) {
            if (element.isValid && !element.isValid()) {
                valid = false
            }
        }
        return valid
    }


    getParams(tag) {
        if (!tag) {
            return []
        }
        let params = []
        try {
            if (typeof tag.parameters != 'object') {
                params = JSON.parse(this._tag.parameters)
            } else { 
                params = tag.parameters
            }
        } catch(e) {
            console.warn(e)
        }
        let value = this._value ? this._value : {}
        let paramsAndValues = []
        if (params) {
            for (let param of params) {
                let parameter = Object.assign({},param)
                if (value[param.name] !== undefined) {
                    parameter.value = value[param.name]
                }
                paramsAndValues.push(parameter)
            }
        }
       /* if (tag.extends_resource != undefined) {
            let extendsRes = Resource.getResource(tag.extends_resource)
            if (extendsRes != undefined) {
                paramsAndValues.push(this.getParams(extendsRes)) 

            }
        }*/
        return paramsAndValues
    }
    
    getParameters() {
        return this.getParams(this._tag)
    }
    
    set value(v) {
        if (v == '') {
            v = {}
        }
        this._value = v;
        this.parameters = this.getParameters()
        this.update('this.value')
        this._elements = [] //they are going to register again
        this.update('this.parameters')
    }

    get value() {
        if (this.elements && this.elements.length > 0) {
            let existingValue = this._value ? this._value : {}
            let associativeObject = this.elements.reduce((obj, item) => {
                obj[item.name] = item.value;
                return obj;
            }, existingValue);
            return associativeObject
        }
        
        return this._value;
    }
}