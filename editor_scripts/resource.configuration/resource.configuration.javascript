class MConfiguration extends Tag {
    setup() {
        this.showdefaults = false
    }
    set resourcename(resourcename) {
        if (this._resourcename == resourcename) {
            return
        }
        this._resourcename = resourcename
        this.update('this.resourcename')
        if (resourcename) {
            resource.action('load_config', { name: resourcename }).then(resource => {
                this.resource = resource
                this.update('this.resource')
                this.rowid = resource.id
                this.update('this.rowid')
 
            });
        }
    }
    get resourcename() {
        return this._resourcename
    }
    resourceUpdated(value) {
        resource.action('update_config', value).then(result=> {
            if(this.resourcename != value.name) {
                document.location = '/editor/resource/'+value.name+'/config'       
            }
        })
    }

}