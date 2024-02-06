class JSettings extends Tag {
    set resource(r) {
        this._resource = r
        this.update('this.resource')
    }
    get resource() {
        return this._resource
    }
    set server(s) {
        this._server = s
        this.update('this.server')
    }
    get server() {
        return this._server
    }
}