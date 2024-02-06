class ViewText extends Tag {
    setup() {
        this.defaulttext = ''
    }
    getDisplayValue() {
        return this.value ? this.value : this.defaulttext;
    }
}