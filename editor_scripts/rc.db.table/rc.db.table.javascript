class RCDBTable extends Tag {
    init() {
        this.updateData()
    }
    set table(t) {
        this._table = t
    }
    get table() {
        return this._table
    }
    updateData() {
        resource.call({table:this.table}).then( data => {
            this.setAttribute('data',data)
            if (data.length > 0) {
                let item = data[0]
                let keys = Object.keys(item)
                this.setAttribute('keys', keys)
            }
        })
    }
}