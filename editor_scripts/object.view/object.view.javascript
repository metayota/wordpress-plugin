class ObjectView extends Tag {
    set value(v) {
        this.obj = v
    }

    get value() {
        return this._obj
    }

    set obj(o) {
		if (o == undefined) {
			o = {}
		}
        this._obj = o 
        this.update('this.obj')
        this.update('this.resource')
    }
    get obj() {
        return this._obj 
    }
    set resourcetype(r) {
        this._resourcetype = r
        Resource.loaded(r).then( resource => {
            this.resource = resource
            this.update('this.resource')
            this.update('this.resourcetype')
        })
    }
    get resourcetype() {
        return this._resourcetype
    }
}