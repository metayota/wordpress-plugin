class JJTable extends Tag {
    set data(d) {
        this._data = d
        this.update('this.data')
    }
    get data() {
        return this._data
    }
    set label(l) {
        this._label = l
    }
    get label() {
        return this._label
    }
    set columns(c) {
        this._columns = c
        this.update('this.columns')
    }
    get columns() {
        return this._columns
    }
    format(v) {
        if (typeof v == 'object') {
            return JSON.stringify(v)
        } else {
            return v
        }
    }
}