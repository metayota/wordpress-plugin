class JDBRowEditor extends Tag {
    set table(t) {
        this._table = t
    }
    get table() {
        return this._table
    }
    set value(v) {
        this._value = v
        this.update('this.value')
    }
    get value() {
        return this._value
    }
    set rowid(rowid) {
        this._rowid = rowid
        this.value = null
        resource.call({action:'load',table:this.table,rowid:this.rowid}).then( value => {
            this.setAttribute('value',value)
        })
    }
    get rowid() {
        return this._rowid
    }
    set resourcetype(r) {
        this._resourcetype = r
        Resource.loaded(r).then( resource => {
            this.resource = resource;
            if (resource.parameters) {
                this.fields = resource.parameters.map( p => p.name )
            } else {
                this.fields = []
            }
            this.update('this.resourcetype')
            this.update('this.resource')
        })
    }
    get resourcetype() {
        return this._resourcetype
    }
    set label(l) {
        this._label = l
        this.update('this.label')
    }
    get label() {
        return this._label
    }
    save(data) {
        let dataToSave = {}
        for (let field of this.fields) {
            dataToSave[field] = data[field]
        }
        resource.call({action:'save',data:dataToSave,table:this.table,rowid:this.rowid}).then(result=> {
            if (window.toast$) {
                toast$.display(result)
            } else if (result.message != undefined) {
                alert(result.message)
            }
        })
        this.fire('save',dataToSave)
    }
}