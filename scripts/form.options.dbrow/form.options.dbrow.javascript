class DBRowOptions extends RCBaseFormElement {

    set value(v) {
        this._value = v
        this.update('this.value')
    }

    get value() {
        return this._value
    }

    changed(value) {
        this.value = value
        this.fire('change',value)
    }

    set dbtable(t) {
        this._dbtable = t
    }

    get dbtable() {
        return this._dbtable
    }

    set titlefield(t) {
        this._titlefield = t
    }

    get titlefield() {
        return this._titlefield
    }

    set idfield(i) {
        this._idfield = i
    }

    get idfield() {
        return this._idfield
    }

    init() {
        this.fetchOptions()
    }

    fetchOptions() {
        if (!this.idfield || !this.titlefield || !this.dbtable) {
            return
        }
        // Tag.callJson('db.fetchall',{table:this.dbtable, fields:[this.idfield,this.titlefield],serverdb:this.serverdb})
        let translate_columns = this.translate_options ?  [this.titlefield] : []

        GlobalResource.call('db.fetchall', {table:this.dbtable, fields:[this.idfield,this.titlefield],serverdb:this.serverdb,translate_columns}, true).then( options => {
            this.options = options.map( v => {
                let translatedProperty = this.titlefield + '_translated'
                let displayValue = v[translatedProperty] != undefined ? v[translatedProperty] : v[this.titlefield]
                return {value: v[this.idfield], name: displayValue}
            })
            this.update('this.options')
        })
    }
}