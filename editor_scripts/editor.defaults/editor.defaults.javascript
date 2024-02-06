class EditorDefaults extends Tag {
    set resourcename(r) {
        this._resourcename = r
        this.update('this.resourcename')
        if (r) {
            resource.action('get_defaults',  {resourcename:r} ).then(configuration => {
                this.setAttribute('defaults', configuration)
            })
        }
    }
    get resourcename() {
        return this._resourcename
    }
    saveDefaults(data) {
        resource.action('save_defaults', { resourcename: this.resourcename, defaults: data })
    }
}