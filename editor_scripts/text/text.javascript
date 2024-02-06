class TextList extends Tag {
    set name(n) {
        this._name = n
        this.text = TextList.getText(n)
        this.update('this.text')
        this.update('this.name')
    }
    get name() {
        return this._name
    }
    static getText(k) {
        return translate(k)
    }
}