class TitleTag extends Tag {
    set title(t) {
        this._title = t
        this.update('this.title')
    }

    get title() {
        return this._title
    }
}